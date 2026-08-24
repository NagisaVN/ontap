<?php

namespace App\Services;

use App\Jobs\AdaptiveQuestionSynthesisJob;
use App\Jobs\GenerateExplainableAIJob;
use App\Models\ExamAttempt;
use App\Models\QuestionOption;
use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ExamGradingService
{
    public function __construct(
        private readonly ExamAttemptRepositoryInterface $luotThiRepo,
        private readonly ProgressTrackingService        $tienDoService,
    ) {}

    /**
     * Chấm bài và cập nhật toàn bộ thống kê.
     *
     * @param  ExamAttempt  $luotThi
     * @param  array        $dapAn   [{cau_hoi_id: x, lua_chon_id: y|null}, ...]
     * @return ExamAttempt
     */
    public function cham(ExamAttempt $luotThi, array $dapAn): ExamAttempt
    {
        return DB::transaction(function () use ($luotThi, $dapAn) {

            $soCauDung   = 0;
            $cauHoiSai   = []; // Câu hỏi làm sai — dùng để dispatch AI job
            $chuongDaLam = []; // Theo dõi các chương đã làm để cập nhật tienDo

            // --- 1. Chấm từng câu ---
            foreach ($dapAn as $item) {
                $cauHoiId   = $item['cau_hoi_id'];
                $luaChonId  = $item['lua_chon_id'] ?? null;

                // Kiểm tra đáp án đúng
                $dungSai = false;
                if ($luaChonId) {
                    $luaChon = QuestionOption::find($luaChonId);
                    $dungSai = $luaChon?->la_dap_an ?? false;
                }

                if ($dungSai) {
                    $soCauDung++;
                } else {
                    $cauHoiSai[] = $cauHoiId;
                }

                // Lưu đáp án vào bảng ket_qua
                $this->luotThiRepo->luuDapAn([
                    'luot_thi_id' => $luotThi->id,
                    'cau_hoi_id'  => $cauHoiId,
                    'lua_chon_id' => $luaChonId,
                    'dung_sai'    => $dungSai,
                    'giai_thich_ai' => null, // Sẽ được AI điền sau
                ]);

                // Cập nhật thống kê câu hỏi
                $thongKe = $this->tienDoService->capNhatThongKeCauHoi(
                    $luotThi->nguoi_dung_id,
                    $cauHoiId,
                    $dungSai
                );

                // Thu thập chương cần cập nhật tiến độ
                $cauHoi = \App\Models\Question::find($cauHoiId);
                if ($cauHoi && !in_array($cauHoi->chuong_id, $chuongDaLam)) {
                    $chuongDaLam[] = $cauHoi->chuong_id;
                }

                // Dispatch Adaptive AI Job nếu sai >= 3 lần
                if (!$dungSai && $thongKe->so_lan_sai >= 3) {
                    AdaptiveQuestionSynthesisJob::dispatch($cauHoiId, $luotThi->nguoi_dung_id)
                        ->onQueue('ai');
                }
            }

            // --- 2. Tính điểm ---
            $tongCau  = count($dapAn);
            $diemSo   = $tongCau > 0
                ? round(($soCauDung / $tongCau) * 10, 2)
                : 0;

            // --- 3. Cập nhật lượt thi ---
            $luotThi = $this->luotThiRepo->capNhat($luotThi, [
                'diem_so'      => $diemSo,
                'so_cau_dung'  => $soCauDung,
                'thoi_gian_lam'=> $luotThi->bat_dau_luc
                    ? now()->diffInSeconds($luotThi->bat_dau_luc)
                    : null,
                'trang_thai'   => 'hoan_thanh',
                'ket_thuc_luc' => now(),
            ]);

            // --- 4. Cập nhật tiến độ từng chương ---
            foreach ($chuongDaLam as $chuongId) {
                $this->tienDoService->capNhatTienDo($luotThi->nguoi_dung_id, $chuongId);
            }

            // --- 5. Dispatch Explainable AI Job cho câu sai ---
            if (!empty($cauHoiSai)) {
                GenerateExplainableAIJob::dispatch($luotThi->id, $cauHoiSai)
                    ->onQueue('ai');
            }

            return $luotThi;
        });
    }
}
