<?php

namespace App\Jobs;

use App\Models\ExamAttemptAnswer;
use App\Models\Question;
use App\Services\GeminiAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateExplainableAIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    /**
     * @param  int    $luotThiId  — ID lượt thi
     * @param  array  $cauHoiSai  — Danh sách cau_hoi_id đã làm sai
     */
    public function __construct(
        public readonly int   $luotThiId,
        public readonly array $cauHoiSai,
    ) {}

    public function handle(GeminiAIService $gemini): void
    {
        Log::info("[GenerateExplainableAIJob] Bắt đầu lượt thi #{$this->luotThiId}", [
            'so_cau_sai' => count($this->cauHoiSai),
        ]);

        foreach ($this->cauHoiSai as $cauHoiId) {
            try {
                // Load đáp án của lượt thi cho câu hỏi này
                $ketQua = ExamAttemptAnswer::where('luot_thi_id', $this->luotThiId)
                    ->where('cau_hoi_id', $cauHoiId)
                    ->first();

                if (!$ketQua || $ketQua->giai_thich_ai) {
                    continue; // Bỏ qua nếu không có hoặc đã có giải thích
                }

                // Load câu hỏi + tất cả lựa chọn
                $cauHoi = Question::with(['luaChon', 'chuong.monHoc'])->find($cauHoiId);
                if (!$cauHoi) continue;

                $luaChonDaSai  = $ketQua->luaChonDaChon;
                $luaChonDung   = $cauHoi->luaChon->firstWhere('la_dap_an', true);
                $tenMonHoc     = $cauHoi->chuong?->monHoc?->ten ?? 'Học thuật';

                if (!$luaChonDung) {
                    Log::warning("[GenerateExplainableAIJob] Câu hỏi #{$cauHoiId} không có đáp án đúng.");
                    continue;
                }

                // Gọi Gemini AI
                $giaiThich = $gemini->giaiThichCauHoi(
                    noiDungCauHoi: $cauHoi->noi_dung,
                    luaChonDaSai:  $luaChonDaSai?->noi_dung ?? '(Bỏ trống)',
                    luaChonDungDan: $luaChonDung->noi_dung,
                    monHoc:        $tenMonHoc,
                );

                // Lưu giải thích vào ket_qua
                $ketQua->update(['giai_thich_ai' => $giaiThich]);

                Log::info("[GenerateExplainableAIJob] Đã giải thích câu #{$cauHoiId}");

            } catch (Throwable $e) {
                // Log lỗi từng câu nhưng không dừng job — tiếp tục các câu khác
                Log::error("[GenerateExplainableAIJob] Lỗi câu #{$cauHoiId}: {$e->getMessage()}");
            }
        }

        Log::info("[GenerateExplainableAIJob] Hoàn tất lượt thi #{$this->luotThiId}");
    }

    public function failed(Throwable $exception): void
    {
        Log::error("[GenerateExplainableAIJob] Job thất bại hoàn toàn lượt thi #{$this->luotThiId}: {$exception->getMessage()}");
    }
}
