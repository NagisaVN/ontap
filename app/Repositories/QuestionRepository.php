<?php

namespace App\Repositories;

use App\Models\Question;
use App\Models\SubSubject;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Support\Collection;

class QuestionRepository implements QuestionRepositoryInterface
{
    /**
     * Lấy tất cả câu hỏi của một môn học (qua chuong).
     */
    public function layTheMonHoc(int $monHocId): Collection
    {
        return Question::daDuyet()
            ->whereHas('chuong', fn($q) => $q->where('mon_hoc_id', $monHocId))
            ->with(['luaChon'])
            ->get();
    }

    /**
     * Lấy câu hỏi theo độ khó, random.
     */
    public function layTheoDoKho(int $monHocId, string $doKho, int $soLuong): Collection
    {
        return Question::daDuyet()
            ->whereHas('chuong', fn($q) => $q->where('mon_hoc_id', $monHocId))
            ->where('do_kho', $doKho)
            ->with(['luaChon'])
            ->inRandomOrder()
            ->limit($soLuong)
            ->get();
    }

    /**
     * Lấy câu hỏi từ một chương cụ thể, random.
     */
    public function layTheoChuong(int $chuongId, int $soLuong): Collection
    {
        return Question::daDuyet()
            ->where('chuong_id', $chuongId)
            ->with(['luaChon'])
            ->inRandomOrder()
            ->limit($soLuong)
            ->get();
    }

    /**
     * Lấy câu hỏi cho Revenge Mode:
     * - User đã làm sai >= 3 lần (hoặc >= 1 lần nếu muốn ôn nhẹ).
     * - Ưu tiên câu sai nhiều nhất.
     */
    public function layChoONTap(int $nguoiDungId, ?int $monHocId = null): Collection
    {
        return Question::whereHas('thongKe', function ($q) use ($nguoiDungId) {
            $q->where('nguoi_dung_id', $nguoiDungId)
              ->where('so_lan_sai', '>=', 1);
        })
        ->when($monHocId, fn($q) => $q->whereHas('chuong', fn($s) => $s->where('mon_hoc_id', $monHocId)))
        ->with(['luaChon', 'thongKe' => fn($q) => $q->where('nguoi_dung_id', $nguoiDungId)])
        ->withSum(['thongKe as tong_sai' => fn($q) => $q->where('nguoi_dung_id', $nguoiDungId)], 'so_lan_sai')
        ->orderByDesc('tong_sai')
        ->get();
    }

    /**
     * Lấy câu hỏi sai của user, kèm thống kê, cho Heatmap.
     */
    public function layCauHoiSai(int $nguoiDungId): Collection
    {
        return Question::whereHas('thongKe', function ($q) use ($nguoiDungId) {
            $q->where('nguoi_dung_id', $nguoiDungId)
              ->where('so_lan_sai', '>=', 1);
        })
        ->with([
            'chuong.monHoc',
            'thongKe' => fn($q) => $q->where('nguoi_dung_id', $nguoiDungId),
        ])
        ->get();
    }
}
