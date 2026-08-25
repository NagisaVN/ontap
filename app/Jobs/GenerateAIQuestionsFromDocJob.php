<?php

namespace App\Jobs;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\GeminiAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateAIQuestionsFromDocJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 300; // Sinh câu hỏi tốn ít thời gian hơn quét toàn bộ PDF

    /**
     * @param  string  $filePath       — Đường dẫn file trong Storage (VD: 'uploads/de_thi.pdf')
     * @param  int     $chuongId       — Chương mặc định để gán câu hỏi (có thể sửa sau)
     * @param  int     $nguoiUploadId  — Teacher đã upload (để notify)
     * @param  string  $mimeType       — MIME type của file
     */
    public function __construct(
        public readonly string $filePath,
        public readonly int    $chuongId,
        public readonly int    $nguoiUploadId,
        public readonly string $mimeType = 'application/pdf',
        public readonly int    $soLuongDe = 4,
        public readonly int    $soLuongTrungBinh = 4,
        public readonly int    $soLuongKho = 2,
    ) {}

    public function handle(GeminiAIService $gemini): void
    {
        Log::info("[GenerateAIQuestionsFromDocJob] Bắt đầu xử lý: {$this->filePath}");

        // 1. Đọc file và encode base64
        if (!Storage::exists($this->filePath)) {
            throw new \RuntimeException("File không tồn tại: {$this->filePath}");
        }

        $fileContent   = Storage::get($this->filePath);
        $base64Content = base64_encode($fileContent);

        // 2. Tạo câu hỏi bằng Gemini
        try {
            $danhSachCauHoi = $gemini->taoCauHoiTuTaiLieu(
                $base64Content, 
                $this->mimeType, 
                $this->soLuongDe,
                $this->soLuongTrungBinh,
                $this->soLuongKho
            );
        } catch (\Exception $e) {
            Log::error("[GenerateAIQuestionsFromDocJob] Gemini API thất bại: " . $e->getMessage(), [
                'exception_class' => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
            ]);
            throw $e;
        }

        if (empty($danhSachCauHoi)) {
            Log::warning("[GenerateAIQuestionsFromDocJob] AI không tạo được câu hỏi nào.");
            \Illuminate\Support\Facades\Cache::put('ocr_job_status_' . md5($this->filePath), 'done', 3600);
            return;
        }

        Log::info("[GenerateAIQuestionsFromDocJob] AI đã tạo " . count($danhSachCauHoi) . " câu hỏi.");

        // 3. Lưu vào DB — trang_thai = cho_duyet (teacher cần duyệt trước khi dùng)
        $soLuuThanh = 0;

        DB::transaction(function () use ($danhSachCauHoi, &$soLuuThanh) {
            foreach ($danhSachCauHoi as $index => $item) {
                try {
                    $this->luuMotCauHoi($item, $index);
                    $soLuuThanh++;
                } catch (Throwable $e) {
                    Log::warning("[GenerateAIQuestionsFromDocJob] Lỗi câu #{$index}: {$e->getMessage()}", [
                        'item' => $item,
                    ]);
                }
            }
        });

        Log::info("[GenerateAIQuestionsFromDocJob] Hoàn tất: {$soLuuThanh}/" . count($danhSachCauHoi) . " câu đã lưu (trạng thái: chờ duyệt).");

        // 4. Báo cho Frontend biết Job đã xong
        \Illuminate\Support\Facades\Cache::put('ocr_job_status_' . md5($this->filePath), 'done', 3600);
    }

    /**
     * Lưu một câu hỏi từ kết quả OCR vào DB.
     */
    private function luuMotCauHoi(array $item, int $index): void
    {
        // Validate cấu trúc tối thiểu
        if (empty($item['noi_dung']) || empty($item['lua_chon'])) {
            throw new \InvalidArgumentException("Câu #{$index} thiếu noi_dung hoặc lua_chon.");
        }

        $luaChon = $item['lua_chon'];
        if (count($luaChon) < 2) {
            throw new \InvalidArgumentException("Câu #{$index} phải có ít nhất 2 lựa chọn.");
        }

        // Normalize do_kho
        $doKho = in_array($item['do_kho'] ?? '', ['de', 'trung_binh', 'kho'])
            ? $item['do_kho']
            : 'trung_binh';

        // Tạo câu hỏi với trang_thai = cho_duyet
        $cauHoi = Question::create([
            'chuong_id'  => $this->chuongId,
            'noi_dung'   => $this->sanitizeNoiDung($item['noi_dung']),
            'do_kho'     => $doKho,
            'giai_thich' => $item['giai_thich'] ?? null,
            'do_ai_tao'  => true,
            'trang_thai' => 'cho_duyet', // ← Bắt buộc qua teacher duyệt
            'nguon'      => 'ocr',
        ]);

        // Tạo các lựa chọn
        foreach ($luaChon as $thuTu => $lc) {
            QuestionOption::create([
                'cau_hoi_id' => $cauHoi->id,
                'noi_dung'   => $this->sanitizeNoiDung($lc['noi_dung'] ?? "Lựa chọn " . ($thuTu + 1)),
                'la_dap_an'  => (bool) ($lc['la_dap_an'] ?? false),
                'thu_tu'     => $thuTu,
            ]);
        }
    }

    /**
     * Xóa số thứ tự đầu dòng mà AI OCR thường giữ lại từ PDF.
     *
     * Xử lý các dạng:
     *   "41. Nội dung"  → "Nội dung"
     *   "3) Nội dung"   → "Nội dung"
     *   "2- Nội dung"   → "Nội dung"
     *   "A. Đáp án"      → "Đáp án"  (cho phương án có prefix A/B/C/D)
     */
    private function sanitizeNoiDung(string $text): string
    {
        // Xoá số thứ tự đầu dòng: "41. ", "3) ", "2- "
        $text = preg_replace('/^\s*\d+[\.\.\-\)]\s+/', '', trim($text));

        // Xoá prefix chữ cái của đáp án: "A. ", "B) ", "C- "
        $text = preg_replace('/^\s*[A-Da-d][\.\.\-\)]\s+/', '', $text);

        return trim($text);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("[GenerateAIQuestionsFromDocJob] Thất bại file {$this->filePath}", [
            'error'           => $exception->getMessage(),
            'exception_class' => get_class($exception),
            'file'            => $exception->getFile(),
            'line'            => $exception->getLine(),
        ]);
        
        // Báo cho Frontend biết Job bị lỗi
        \Illuminate\Support\Facades\Cache::put('ocr_job_status_' . md5($this->filePath), 'error', 3600);
    }
}
