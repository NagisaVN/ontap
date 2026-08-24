<?php

namespace App\Repositories;

use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ExamAttemptRepository implements ExamAttemptRepositoryInterface
{
    /**
     * Tạo lượt thi mới với trạng thái "dang_lam".
     */
    public function taoLuotThi(array $data): ExamAttempt
    {
        return ExamAttempt::create(array_merge($data, [
            'trang_thai'  => 'dang_lam',
            'bat_dau_luc' => now(),
        ]));
    }

    /**
     * Lưu một đáp án vào bảng ket_qua.
     */
    public function luuDapAn(array $data): ExamAttemptAnswer
    {
        return ExamAttemptAnswer::updateOrCreate(
            [
                'luot_thi_id' => $data['luot_thi_id'],
                'cau_hoi_id'  => $data['cau_hoi_id'],
            ],
            $data
        );
    }

    /**
     * Tìm lượt thi đang làm dở (trang_thai = dang_lam).
     */
    public function timDangLam(int $nguoiDungId, int $baiThiId): ?ExamAttempt
    {
        return ExamAttempt::where('nguoi_dung_id', $nguoiDungId)
            ->where('bai_thi_id', $baiThiId)
            ->where('trang_thai', 'dang_lam')
            ->latest()
            ->first();
    }

    /**
     * Cập nhật điểm số và trạng thái sau khi chấm bài.
     */
    public function capNhat(ExamAttempt $luotThi, array $data): ExamAttempt
    {
        $luotThi->update($data);
        return $luotThi->fresh();
    }

    /**
     * Auto-save: Lưu tạm danh sách đáp án đã chọn vào cache (30 giây interval từ Livewire).
     * Key: luot_thi_{id}_tu_dong_luu
     */
    public function tuDongLuu(int $luotThiId, array $dapAnTamThoi): void
    {
        Cache::put(
            "luot_thi_{$luotThiId}_tu_dong_luu",
            $dapAnTamThoi,
            now()->addHours(4) // Cache 4 tiếng, đủ cho bài thi dài nhất
        );
    }
}
