<?php

namespace App\Livewire\Student;

use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Services\GeminiAIService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kết quả bài thi')]
class ExamResult extends Component
{
    public ExamAttempt $luotThi;

    // ── AI Smart Tutor ─────────────────────────────────────────────────
    public ?int   $activeChatKetQuaId = null;
    public array  $chatHistory        = [];
    public string $userMessage        = '';
    public string $tutorError         = '';

    public function mount(ExamAttempt $luotThi): void
    {
        abort_unless($luotThi->nguoi_dung_id === Auth::id(), 403);
        $this->luotThi = $luotThi;
    }

    // ── Open Tutor ─────────────────────────────────────────────────────
    /**
     * Mở cửa sổ chat AI Tutor cho một câu hỏi sai/bỏ qua.
     * @param int $ketQuaId  ExamAttemptAnswer->id
     */
    public function openTutor(int $ketQuaId): void
    {
        $ketQua = ExamAttemptAnswer::with('cauHoi.luaChon')
            ->where('luot_thi_id', $this->luotThi->id)
            ->find($ketQuaId);

        if (!$ketQua) return;

        $cauHoi      = $ketQua->cauHoi;
        $dapAnDung   = $cauHoi->luaChon->firstWhere('la_dap_an', true);
        $dapAnChon   = $ketQua->luaChonDaChon;
        $monHoc      = $this->luotThi->baiThi->monHoc?->ten ?? 'môn học';

        $dapAnChonText = $dapAnChon?->noi_dung ?? 'Bỏ trống (không chọn)';
        $dapAnDungText = $dapAnDung?->noi_dung ?? 'Không rõ';
        $cauHoiText    = $cauHoi->noi_dung;

        $systemPrompt = <<<PROMPT
Bạn là gia sư AI thông minh của SmartPrep — một nền tảng luyện thi trực tuyến.
Nhiệm vụ của bạn là giúp học sinh hiểu TẠI SAO câu trả lời của họ sai và học cách tư duy đúng.

THÔNG TIN CÂU HỎI:
- Môn học: {$monHoc}
- Câu hỏi: "{$cauHoiText}"
- Đáp án học sinh chọn: "{$dapAnChonText}"
- Đáp án đúng: "{$dapAnDungText}"

NGUYÊN TẮC PHẢN HỒI:
1. Dùng tiếng Việt, ngắn gọn, thân thiện như người thầy gần gũi
2. KHÔNG chỉ nói "đáp án đúng là X" — hãy GIẢI THÍCH tại sao
3. Khuyến khích học sinh suy nghĩ bằng câu hỏi dẫn dắt khi cần
4. Tối đa 150 từ mỗi phản hồi, trừ khi học sinh hỏi chi tiết hơn
5. Khi học sinh đã hiểu, hãy tóm tắt bằng 1 câu ghi nhớ ngắn

Hãy bắt đầu bằng lời chào ngắn và hỏi học sinh muốn hiểu điểm nào.
PROMPT;

        $this->activeChatKetQuaId = $ketQuaId;
        $this->tutorError         = '';
        $this->userMessage        = '';
        $this->chatHistory        = [
            // System prompt ẩn — không hiển thị trong UI
            ['role' => 'system', 'content' => $systemPrompt],
            // Greeting tĩnh ngay lập tức — không block UI
            ['role' => 'model', 'content' => "Xin chào! Tôi là Gia Sư AI của SmartPrep \u{1F916}\n\nTôi thấy bạn đã chọn **\"{$dapAnChonText}\"** nhưng đáp án đúng là **\"{$dapAnDungText}\"**. Bạn muốn tôi giải thích điều gì khó hiểu nhất?"],
        ];
    }

    // ── Close Tutor ────────────────────────────────────────────────────
    public function closeTutor(): void
    {
        $this->activeChatKetQuaId = null;
        $this->chatHistory        = [];
        $this->userMessage        = '';
        $this->tutorError         = '';
    }

    // ── Send Message ───────────────────────────────────────────────────
    public function sendMessage(): void
    {
        $message = trim($this->userMessage);
        if (empty($message) || $this->activeChatKetQuaId === null) return;

        $this->tutorError = '';

        // Append user message to history
        $this->chatHistory[] = ['role' => 'user', 'content' => $message];
        $this->userMessage   = '';

        try {
            $apiKey  = config('gemini.api_key');
            $model   = config('gemini.model', 'gemini-3.6-flash');
            $version = config('gemini.api_version', 'v1beta');
            $baseUrl = config('gemini.base_url', 'https://generativelanguage.googleapis.com');
            $url     = "{$baseUrl}/{$version}/models/{$model}:generateContent?key={$apiKey}";

            // Build contents array (skip system role — move to system_instruction)
            $systemPrompt = '';
            $contents     = [];
            foreach ($this->chatHistory as $msg) {
                if ($msg['role'] === 'system') {
                    $systemPrompt = $msg['content'];
                    continue;
                }
                $contents[] = [
                    'role'  => $msg['role'],
                    'parts' => [['text' => $msg['content']]],
                ];
            }

            $payload = [
                'system_instruction' => ['parts' => [['text' => $systemPrompt]]],
                'contents'           => $contents,
                'generationConfig'   => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 512,
                    'topP'            => 0.9,
                ],
                'safetySettings' => config('gemini.safety_settings'),
            ];

            $response = Http::timeout(config('gemini.timeout', 60))
                ->post($url, $payload);

            if ($response->failed()) {
                $errMsg = $response->json('error.message', 'Không xác định');
                throw new \RuntimeException("Gemini API lỗi {$response->status()}: {$errMsg}");
            }

            $aiText = $response->json('candidates.0.content.parts.0.text', '');

            if (empty($aiText)) {
                $finish = $response->json('candidates.0.finishReason', 'UNKNOWN');
                throw new \RuntimeException("AI trả về phản hồi trống. Lý do: {$finish}");
            }

            $this->chatHistory[] = ['role' => 'model', 'content' => trim($aiText)];

        } catch (\Throwable $e) {
            Log::error('[ExamResult/AI Tutor] sendMessage thất bại: ' . $e->getMessage());
            // Remove the user message we just added since AI didn't reply
            array_pop($this->chatHistory);
            $this->userMessage = $message;
            $this->tutorError  = 'AI tạm không khả dụng: ' . $e->getMessage();
        }
    }

    // ── Render ──────────────────────────────────────────────────────────
    public function render()
    {
        $this->luotThi->refresh()->load([
            'baiThi.monHoc',
            'ketQua.cauHoi.luaChon',
            'ketQua.luaChonDaChon',
        ]);

        $baiThi  = $this->luotThi->baiThi;
        $tongCau = $this->luotThi->ketQua->count();
        $soDung  = $this->luotThi->so_cau_dung;
        $soSai   = $tongCau - $soDung;
        $soBo    = $this->luotThi->ketQua->whereNull('lua_chon_id')->count();

        $percentage = $tongCau > 0
            ? round($soDung / $tongCau * 100)
            : round(($this->luotThi->diem_so ?? 0) * 10);

        $thoiGianGiay = $this->luotThi->thoi_gian_lam ?? 0;
        $timeUsed = $thoiGianGiay > 0
            ? sprintf('%dm %ds', intdiv($thoiGianGiay, 60), $thoiGianGiay % 60)
            : '—';

        $rank = match(true) {
            $percentage >= 90 => ['label' => 'Xuất sắc',    'color' => 'emerald', 'emoji' => '🏆'],
            $percentage >= 80 => ['label' => 'Giỏi',        'color' => 'indigo',  'emoji' => '🌟'],
            $percentage >= 65 => ['label' => 'Khá',         'color' => 'blue',    'emoji' => '👍'],
            $percentage >= 50 => ['label' => 'Đạt',         'color' => 'amber',   'emoji' => '👌'],
            default           => ['label' => 'Cần cố gắng', 'color' => 'rose',    'emoji' => '💪'],
        };

        $reviewItems = $this->luotThi->ketQua->map(function ($kq, $i) {
            $cauHoi        = $kq->cauHoi;
            $dapAnDung     = $cauHoi->luaChon->firstWhere('la_dap_an', true);
            $luaChonDaChon = $kq->luaChonDaChon;

            $status = is_null($kq->lua_chon_id)
                ? 'skipped'
                : ($kq->dung_sai ? 'correct' : 'wrong');

            return [
                'id'            => $kq->id,
                'cauHoiId'      => $cauHoi->id,
                'question'      => $cauHoi->noi_dung,
                'status'        => $status,
                'userAnswer'    => $luaChonDaChon?->noi_dung,
                'correctAnswer' => $dapAnDung?->noi_dung,
                'explanation'   => $kq->giai_thich_ai,
            ];
        })->values()->toArray();

        return view('livewire.student.exam-result', [
            'baiThi'      => $baiThi,
            'examTitle'   => ($baiThi->monHoc?->ten ?? '') . ' – ' . $baiThi->ten_bai_thi,
            'duration'    => $timeUsed,
            'tongCau'     => $tongCau,
            'score'       => $soDung,
            'total'       => $tongCau,
            'percentage'  => $percentage,
            'correct'     => $soDung,
            'wrong'       => $soSai,
            'skipped'     => $soBo,
            'timeUsed'    => $timeUsed,
            'rank'        => $rank,
            'reviewItems' => $reviewItems,
        ]);
    }

    public function generateExplanation(int $ketQuaId, GeminiAIService $gemini): void
    {
        $ketQua = ExamAttemptAnswer::with('cauHoi.luaChon')
            ->where('luot_thi_id', $this->luotThi->id)
            ->find($ketQuaId);

        if (!$ketQua || $ketQua->giai_thich_ai) return;

        try {
            $cauHoi        = $ketQua->cauHoi;
            $luaChonDaChon = $ketQua->luaChonDaChon;
            $luaChonDung   = $cauHoi->luaChon->firstWhere('la_dap_an', true);

            $giaiThich = $gemini->giaiThichCauHoi(
                $cauHoi->noi_dung,
                $luaChonDaChon?->noi_dung ?? 'Không chọn đáp án nào (bỏ trống)',
                $luaChonDung?->noi_dung   ?? 'Không có đáp án đúng',
                $this->luotThi->baiThi->monHoc?->ten ?? ''
            );

            $ketQua->update(['giai_thich_ai' => $giaiThich]);
            $this->dispatch('explanation-generated', ketQuaId: $ketQuaId);

        } catch (\Throwable $e) {
            $msg   = $e->getMessage();
            $delay = 60;
            if (preg_match('/Please retry in ([\d\.]+)s/', $msg, $m)) {
                $delay = (int) ceil((float) $m[1]);
            }
            if (str_contains($msg, '429') || str_contains($msg, 'quota')) {
                session()->flash('error_' . $ketQuaId, "Hệ thống AI tạm hết quota. Vui lòng đợi {$delay} giây rồi thử lại!");
            } else {
                session()->flash('error_' . $ketQuaId, 'Lỗi AI: ' . $msg);
            }
        }
    }
}
