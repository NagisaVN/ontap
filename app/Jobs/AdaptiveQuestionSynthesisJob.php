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
use Throwable;

class AdaptiveQuestionSynthesisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 20;
    public int $timeout = 180;

    /**
     * @param  int  $cauHoiId     — Câu hỏi gốc cần sinh biến thể
     * @param  int  $nguoiDungId  — User để log context
     */
    public function __construct(
        public readonly int $cauHoiId,
        public readonly int $nguoiDungId,
    ) {}

    public function handle(GeminiAIService $gemini): void
    {
        Log::info("[AdaptiveQuestionSynthesisJob] Sinh biến thể câu #{$this->cauHoiId}");

        // Load câu hỏi gốc + lựa chọn
        $cauHoiGoc = Question::with(['luaChon', 'chuong'])->find($this->cauHoiId);
        if (!$cauHoiGoc) {
            Log::warning("[AdaptiveQuestionSynthesisJob] Câu hỏi #{$this->cauHoiId} không tồn tại.");
            return;
        }

        // Kiểm tra đã có đủ biến thể chưa (tránh sinh trùng)
        $soBienTheDaCoEm = Question::where('cau_hoi_goc_id', $this->cauHoiId)->count();
        if ($soBienTheDaCoEm >= 3) {
            Log::info("[AdaptiveQuestionSynthesisJob] Câu #{$this->cauHoiId} đã có {$soBienTheDaCoEm} biến thể, bỏ qua.");
            return;
        }

        $canSinh = 3 - $soBienTheDaCoEm;

        // Build array lựa chọn để gửi AI
        $luaChonGoc = $cauHoiGoc->luaChon->map(fn($lc) => [
            'noi_dung'  => $lc->noi_dung,
            'la_dap_an' => $lc->la_dap_an,
        ])->toArray();

        try {
            // Gọi Gemini AI
            $bienThe = $gemini->sinhBienTheCauHoi(
                noiDungGoc: $cauHoiGoc->noi_dung,
                luaChonGoc: $luaChonGoc,
                soLuong:    $canSinh,
            );
        } catch (Throwable $e) {
            $errorMsg = $e->getMessage();
            Log::error("[AdaptiveQuestionSynthesisJob] Lỗi câu #{$this->cauHoiId}: {$errorMsg}");
            
            // Nếu dính Rate Limit (429), nhả Job về Queue để thử lại sau
            if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'Too Many Requests') || str_contains($errorMsg, 'quota')) {
                $delay = 30; // Mặc định 30 giây
                if (preg_match('/Please retry in ([\d\.]+)s/', $errorMsg, $matches)) {
                    $delay = (int) ceil((float) $matches[1]) + 5; // Lấy thời gian Google yêu cầu + 5s bù trừ
                }
                Log::warning("[AdaptiveQuestionSynthesisJob] Dính Rate Limit 429. Trì hoãn {$delay} giây rồi thử lại...");
                $this->release($delay);
                return;
            }
            throw $e; // Throw lại nếu không phải lỗi 429
        }

        if (empty($bienThe)) {
            Log::warning("[AdaptiveQuestionSynthesisJob] AI không sinh được biến thể cho câu #{$this->cauHoiId}");
            return;
        }

        // Lưu các biến thể vào DB trong transaction
        DB::transaction(function () use ($cauHoiGoc, $bienThe) {
            foreach ($bienThe as $index => $bt) {
                // Kiểm tra cấu trúc hợp lệ
                if (empty($bt['noi_dung']) || empty($bt['lua_chon'])) {
                    Log::warning("[AdaptiveQuestionSynthesisJob] Biến thể #{$index} thiếu dữ liệu, bỏ qua.");
                    continue;
                }

                // Tạo câu hỏi biến thể
                $cauHoiMoi = Question::create([
                    'chuong_id'      => $cauHoiGoc->chuong_id,
                    'noi_dung'       => $bt['noi_dung'],
                    'do_kho'         => $cauHoiGoc->do_kho->value,
                    'giai_thich'     => $bt['giai_thich'] ?? null,
                    'do_ai_tao'      => true,
                    'cau_hoi_goc_id' => $cauHoiGoc->id,
                    'trang_thai'     => 'da_duyet', // AI biến thể → dùng được ngay
                    'nguon'          => 'ai_sinh',
                ]);

                // Tạo các lựa chọn
                foreach ($bt['lua_chon'] as $thuTu => $luaChon) {
                    QuestionOption::create([
                        'cau_hoi_id' => $cauHoiMoi->id,
                        'noi_dung'   => $luaChon['noi_dung'],
                        'la_dap_an'  => $luaChon['la_dap_an'] ?? false,
                        'thu_tu'     => $thuTu,
                    ]);
                }

                Log::info("[AdaptiveQuestionSynthesisJob] Đã tạo biến thể ID #{$cauHoiMoi->id} từ câu #{$cauHoiGoc->id}");
            }
        });

        Log::info("[AdaptiveQuestionSynthesisJob] Hoàn tất câu #{$this->cauHoiId} — đã sinh " . count($bienThe) . " biến thể.");
    }

    public function failed(Throwable $exception): void
    {
        Log::error("[AdaptiveQuestionSynthesisJob] Job thất bại câu #{$this->cauHoiId}: {$exception->getMessage()}");
    }
}
