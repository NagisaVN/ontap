<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('OCR Upload — SmartPrep')]
class OcrUpload extends Component
{
    use WithFileUploads;

    // ── Upload ──────────────────────────────────────────────────────────
    #[Validate('required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240')]
    public $file = null;

    // ── Config ──────────────────────────────────────────────────────────
    /** 'extract' = trích xuất câu hỏi có sẵn | 'generate' = tự soạn câu hỏi */
    public string $mode = 'extract';

    /** Chapter đích khi lưu vào DB */
    #[Validate('required|integer|min:1|exists:chuong,id')]
    public int $chuongId = 0;

    /** Số lượng câu theo độ khó khi mode = generate */
    public int $soLuongDe        = 5;
    public int $soLuongTrungBinh = 3;
    public int $soLuongKho       = 2;

    // ── State ────────────────────────────────────────────────────────────
    public array   $extractedQuestions = [];
    public array   $savedIndices       = [];   // indices đã lưu riêng lẻ
    public bool    $allSaved           = false;
    public string  $errorMessage       = '';
    public string  $successMessage     = '';
    public bool    $processing         = false;

    // ── Pagination kết quả ───────────────────────────────────────────────
    public int $resultsPage = 1;
    public int $perPage     = 20;

    // ── Reset ────────────────────────────────────────────────────────────
    public function clearFile(): void
    {
        $this->file               = null;
        $this->extractedQuestions = [];
        $this->savedIndices       = [];
        $this->allSaved           = false;
        $this->errorMessage       = '';
        $this->successMessage     = '';
        $this->resultsPage        = 1;
    }

    // ── Result pagination helpers ─────────────────────────────────────────
    public function getPagedQuestions(): array
    {
        return array_slice($this->extractedQuestions, ($this->resultsPage - 1) * $this->perPage, $this->perPage, true);
    }

    public function getTotalPages(): int
    {
        return (int) ceil(count($this->extractedQuestions) / $this->perPage);
    }

    public function nextResultPage(): void
    {
        if ($this->resultsPage < $this->getTotalPages()) $this->resultsPage++;
    }

    public function prevResultPage(): void
    {
        if ($this->resultsPage > 1) $this->resultsPage--;
    }

    public function gotoResultPage(int $page): void
    {
        $this->resultsPage = max(1, min($page, $this->getTotalPages()));
    }

    public function backToUpload(): void
    {
        $this->extractedQuestions = [];
        $this->savedIndices       = [];
        $this->allSaved           = false;
        $this->successMessage     = '';
        $this->errorMessage       = '';
        $this->resultsPage        = 1;
    }

    public function updatedFile(): void
    {
        $this->extractedQuestions = [];
        $this->savedIndices       = [];
        $this->allSaved           = false;
        $this->errorMessage       = '';
        $this->successMessage     = '';
        $this->resultsPage        = 1;
    }

    // ── Core: Inline AI OCR (synchronous — kết quả hiện ngay) ───────────
    /**
     * Gửi file lên Gemini API đồng bộ và lưu kết quả vào $extractedQuestions.
     * Dùng GeminiAIService đã có sẵn trong codebase.
     */
    public function processOcr(): void
    {
        // Cho phép PHP chạy tới 5 phút cho request nặng
        set_time_limit(300);

        $this->errorMessage   = '';
        $this->successMessage = '';
        $this->extractedQuestions = [];
        $this->savedIndices   = [];
        $this->allSaved       = false;

        // Validate file
        try {
            $this->validateOnly('file');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->errorMessage = $e->validator->errors()->first('file');
            return;
        }

        if (!$this->file) {
            $this->errorMessage = 'Vui lòng chọn file trước khi phân tích.';
            return;
        }

        try {
            $this->processing = true;

            $mimeType      = $this->file->getMimeType();
            $base64Content = base64_encode(file_get_contents($this->file->getRealPath()));

            /** @var GeminiAIService $gemini */
            $gemini = app(GeminiAIService::class);

            if ($this->mode === 'generate') {
                $this->soLuongDe        = max(0, (int) $this->soLuongDe);
                $this->soLuongTrungBinh = max(0, (int) $this->soLuongTrungBinh);
                $this->soLuongKho       = max(0, (int) $this->soLuongKho);

                $total = $this->soLuongDe + $this->soLuongTrungBinh + $this->soLuongKho;
                if ($total < 1 || $total > 50) {
                    $this->errorMessage = 'Tổng số câu phải từ 1 đến 50.';
                    return;
                }

                $questions = $gemini->taoCauHoiTuTaiLieu(
                    $base64Content,
                    $mimeType,
                    $this->soLuongDe,
                    $this->soLuongTrungBinh,
                    $this->soLuongKho
                );
            } else {
                // ── Vòng lặp trích xuất: tiếp tục gọi cho đến khi hết câu ──
                $questions = $gemini->ocrTrichXuatCauHoi($base64Content, $mimeType);

                $maxRounds = 5;  // tối đa 5 lần gọi (≈ 5×80 = 400 câu)
                $round     = 0;

                while ($round < $maxRounds && count($questions) > 0) {
                    $lastQuestion = end($questions);
                    $lastText     = $lastQuestion['noi_dung'] ?? '';

                    if (empty($lastText)) break;

                    // Thử lấy thêm câu phía sau câu cuối cùng
                    $more = $gemini->ocrTrichXuatTiepTheo($base64Content, $mimeType, $lastText);

                    if (empty($more)) break;  // không còn câu nào nữa

                    $questions = array_merge($questions, $more);
                    $round++;
                }
            }


            if (empty($questions)) {
                $this->errorMessage = 'AI không tìm thấy câu hỏi trắc nghiệm nào trong tài liệu. Thử chọn file khác hoặc đổi chế độ.';
                return;
            }

            $this->extractedQuestions = $questions;
            $this->successMessage = 'Trích xuất thành công ' . count($questions) . ' câu hỏi!';

        } catch (\Throwable $e) {
            Log::error('[OcrUpload] processOcr thất bại: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'mode'    => $this->mode,
            ]);
            $this->errorMessage = 'Lỗi khi gọi AI: ' . $e->getMessage();
        } finally {
            $this->processing = false;
        }
    }

    // ── Save: Lưu từng câu ──────────────────────────────────────────────
    public function saveQuestion(int $index): void
    {
        if (!isset($this->extractedQuestions[$index])) return;
        if (in_array($index, $this->savedIndices)) return;
        if (!$this->chuongId) {
            $this->errorMessage = 'Vui lòng chọn chương đích trước khi lưu.';
            return;
        }

        $this->persistQuestion($this->extractedQuestions[$index]);
        $this->savedIndices[] = $index;
        $this->errorMessage   = '';

        if (count($this->savedIndices) === count($this->extractedQuestions)) {
            $this->allSaved       = true;
            $this->successMessage = 'Đã lưu tất cả câu hỏi vào ngân hàng!';
        }
    }

    // ── Save: Lưu tất cả ────────────────────────────────────────────────
    public function saveAllToDatabase(): void
    {
        if (empty($this->extractedQuestions)) return;
        if (!$this->chuongId) {
            $this->errorMessage = 'Vui lòng chọn chương đích trước khi lưu.';
            return;
        }

        $saved = 0;
        foreach ($this->extractedQuestions as $i => $q) {
            if (in_array($i, $this->savedIndices)) continue;
            $this->persistQuestion($q);
            $this->savedIndices[] = $i;
            $saved++;
        }

        $this->allSaved       = true;
        $this->errorMessage   = '';
        $this->successMessage = "Đã lưu {$saved} câu hỏi vào ngân hàng (chờ duyệt).";
    }

    // ── Helper: Ghi 1 câu hỏi vào DB ────────────────────────────────────
    protected function persistQuestion(array $q): void
    {
        DB::transaction(function () use ($q) {
            $question = Question::create([
                'chuong_id'   => $this->chuongId,
                'noi_dung'    => $q['noi_dung']     ?? ($q['question_text'] ?? ''),
                'do_kho'      => $this->normalizeDoKho($q['do_kho'] ?? ($q['difficulty'] ?? 'trung_binh')),
                'giai_thich'  => $q['giai_thich']   ?? ($q['explanation'] ?? null),
                'trang_thai'  => 'cho_duyet',
                'do_ai_tao'   => true,
            ]);

            $options = $q['lua_chon'] ?? $this->buildOptionsFromKeys($q);

            foreach ($options as $i => $opt) {
                QuestionOption::create([
                    'cau_hoi_id' => $question->id,
                    'noi_dung'   => $opt['noi_dung']  ?? $opt['text'] ?? '',
                    'la_dap_an'  => (bool) ($opt['la_dap_an'] ?? $opt['is_correct'] ?? false),
                    'thu_tu'     => $i + 1,
                ]);
            }
        });
    }

    /** Chuyển "Easy/Medium/Hard/de/trung_binh/kho" về enum value */
    protected function normalizeDoKho(string $raw): string
    {
        return match (strtolower(trim($raw))) {
            'easy',   'de'         => 'de',
            'hard',   'kho'        => 'kho',
            default                => 'trung_binh',
        };
    }

    /** Fallback: xây mảng lua_chon từ option_a/b/c/d + correct_answer */
    protected function buildOptionsFromKeys(array $q): array
    {
        $correct = strtoupper(trim($q['correct_answer'] ?? ''));
        $opts    = [];
        foreach (['A', 'B', 'C', 'D'] as $letter) {
            $key = 'option_' . strtolower($letter);
            if (!isset($q[$key])) continue;
            $opts[] = [
                'noi_dung'  => $q[$key],
                'la_dap_an' => ($correct === $letter),
            ];
        }
        return $opts;
    }

    // ── Render ───────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.teacher.ocr-upload', [
            'monHocs' => Subject::with(['chuong' => fn($q) => $q->orderBy('thu_tu')])->orderBy('ten')->get(),
        ]);
    }
}
