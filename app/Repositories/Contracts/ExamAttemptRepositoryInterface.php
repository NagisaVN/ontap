<?php

namespace App\Repositories\Contracts;

use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;

interface ExamAttemptRepositoryInterface
{
    /**
     * Tạo một lượt thi mới.
     */
    public function taoLuotThi(array $data): ExamAttempt;

    /**
     * Lưu đáp án của một câu hỏi.
     */
    public function luuDapAn(array $data): ExamAttemptAnswer;

    /**
     * Tìm lượt thi đang làm dở của user.
     */
    public function timDangLam(int $nguoiDungId, int $baiThiId): ?ExamAttempt;

    /**
     * Cập nhật trạng thái & điểm của lượt thi.
     */
    public function capNhat(ExamAttempt $luotThi, array $data): ExamAttempt;

    /**
     * Auto-save trạng thái mỗi 30 giây (chỉ lưu các câu đã chọn).
     */
    public function tuDongLuu(int $luotThiId, array $dapAnTamThoi): void;
}
