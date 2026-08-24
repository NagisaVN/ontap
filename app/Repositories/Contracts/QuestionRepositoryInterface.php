<?php

namespace App\Repositories\Contracts;

use App\Models\Question;
use Illuminate\Support\Collection;

interface QuestionRepositoryInterface
{
    /**
     * Lấy tất cả câu hỏi của một môn học (qua các chương).
     */
    public function layTheMonHoc(int $monHocId): Collection;

    /**
     * Lấy câu hỏi theo độ khó trong một môn học.
     */
    public function layTheoDoKho(int $monHocId, string $doKho, int $soLuong): Collection;

    /**
     * Lấy câu hỏi theo chương cụ thể.
     */
    public function layTheoChuong(int $chuongId, int $soLuong): Collection;

    /**
     * Lấy câu hỏi cho Revenge Mode: những câu sai >= 3 lần của user.
     */
    public function layChoONTap(int $nguoiDungId, ?int $monHocId = null): Collection;

    /**
     * Lấy câu hỏi sai nhiều (cho heatmap), nhóm theo chương.
     */
    public function layCauHoiSai(int $nguoiDungId): Collection;
}
