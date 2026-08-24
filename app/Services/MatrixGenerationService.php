<?php

namespace App\Services;

use App\Enums\ExamMode;
use App\Models\Exam;
use App\Models\SubSubject;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class MatrixGenerationService
{
    public function __construct(
        private readonly QuestionRepositoryInterface $cauHoiRepo,
    ) {}

    /**
     * Sinh đề thi dựa trên chế độ (che_do) của bài thi.
     * Lưu kết quả vào bảng pivot bai_thi_cau_hoi.
     *
     * @throws InvalidArgumentException
     */
    public function generate(Exam $baiThi): Collection
    {
        $cauHoi = match ($baiThi->che_do) {
            ExamMode::NgauNhien   => $this->ngauNhien($baiThi),
            ExamMode::TheoDoKho   => $this->theoDoKho($baiThi),
            ExamMode::TyLeTuyChon => $this->tyLeTuyChon($baiThi),
        };

        // Shuffle lần cuối để tránh đoán thứ tự
        $cauHoi = $cauHoi->shuffle();

        // Lưu vào pivot bai_thi_cau_hoi với thứ tự
        $syncData = $cauHoi->values()->mapWithKeys(function ($cq, $index) {
            return [$cq->id => ['thu_tu' => $index + 1]];
        })->toArray();

        $baiThi->cauHoi()->sync($syncData);

        return $cauHoi;
    }

    // ---------------------------------------------------------------
    // PRIVATE: Ngẫu nhiên
    // ---------------------------------------------------------------

    private function ngauNhien(Exam $baiThi): Collection
    {
        $tatCa = $this->cauHoiRepo->layTheMonHoc($baiThi->mon_hoc_id);

        if ($tatCa->count() < $baiThi->so_cau_hoi) {
            throw new InvalidArgumentException(
                "Môn học không đủ câu hỏi. Yêu cầu {$baiThi->so_cau_hoi}, hiện có {$tatCa->count()}."
            );
        }

        return $tatCa->random($baiThi->so_cau_hoi);
    }

    // ---------------------------------------------------------------
    // PRIVATE: Theo độ khó
    // Phân phối mặc định: 30% dễ / 50% trung bình / 20% khó
    // Có thể override qua cau_hinh_ma_tran: {de: 30, trung_binh: 50, kho: 20}
    // ---------------------------------------------------------------

    private function theoDoKho(Exam $baiThi): Collection
    {
        $cauHinh   = $baiThi->cau_hinh_ma_tran ?? ['de' => 30, 'trung_binh' => 50, 'kho' => 20];
        $tongCau   = $baiThi->so_cau_hoi;
        $monHocId  = $baiThi->mon_hoc_id;
        $ketQua    = collect();

        $soLuong = [
            'de'         => (int) round($tongCau * ($cauHinh['de']         ?? 30) / 100),
            'trung_binh' => (int) round($tongCau * ($cauHinh['trung_binh'] ?? 50) / 100),
            'kho'        => (int) round($tongCau * ($cauHinh['kho']        ?? 20) / 100),
        ];

        // Điều chỉnh làm tròn để tổng = tongCau
        $chenh = $tongCau - array_sum($soLuong);
        $soLuong['trung_binh'] += $chenh;

        foreach ($soLuong as $doKho => $sl) {
            if ($sl <= 0) continue;
            $cauHoi = $this->cauHoiRepo->layTheoDoKho($monHocId, $doKho, $sl);
            $ketQua = $ketQua->merge($cauHoi);
        }

        if ($ketQua->count() < $tongCau) {
            // Bổ sung ngẫu nhiên nếu không đủ câu theo độ khó
            $conThieu  = $tongCau - $ketQua->count();
            $daCo      = $ketQua->pluck('id');
            $boSung    = $this->cauHoiRepo->layTheMonHoc($monHocId)
                ->whereNotIn('id', $daCo)
                ->take($conThieu);
            $ketQua = $ketQua->merge($boSung);
        }

        return $ketQua;
    }

    // ---------------------------------------------------------------
    // PRIVATE: Tỷ lệ tùy chỉnh theo chương
    // cau_hinh_ma_tran: [{chuong_id: 1, phan_tram: 40}, {chuong_id: 2, phan_tram: 60}]
    // ---------------------------------------------------------------

    private function tyLeTuyChon(Exam $baiThi): Collection
    {
        $cauHinh = $baiThi->cau_hinh_ma_tran;

        if (empty($cauHinh)) {
            throw new InvalidArgumentException('Cấu hình ma trận không được để trống khi dùng chế độ tỷ lệ tùy chỉnh.');
        }

        $tongCau  = $baiThi->so_cau_hoi;
        $ketQua   = collect();
        $tongPhan = array_sum(array_column($cauHinh, 'phan_tram'));

        foreach ($cauHinh as $index => $muc) {
            $chuongId  = $muc['chuong_id'];
            $phanTram  = $muc['phan_tram'];

            // Làm tròn, mục cuối lấy phần còn lại để đảm bảo tổng đúng
            $soLuong = ($index === array_key_last($cauHinh))
                ? $tongCau - $ketQua->count()
                : (int) round($tongCau * $phanTram / $tongPhan);

            if ($soLuong <= 0) continue;

            $cauHoi  = $this->cauHoiRepo->layTheoChuong($chuongId, $soLuong);
            $ketQua  = $ketQua->merge($cauHoi);
        }

        return $ketQua;
    }
}
