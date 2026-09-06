<?php

namespace App\Livewire\Student;

use App\Enums\ExamMode;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\SubSubject;
use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use App\Services\MatrixGenerationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Tạo bài thi')]
class CreateExam extends Component
{
    #[Validate('required|exists:mon_hoc,id')]
    public int $monHocId = 0;

    #[Validate('required|in:ngau_nhien,theo_do_kho,ty_le_tuy_chon')]
    public string $cheDoThi = 'ngau_nhien';

    #[Validate('required|integer|min:5|max:100')]
    public int $soCauHoi = 40;

    #[Validate('required|integer|min:5|max:120')]
    public int $thoiGianPhut = 45;

    public string $tenBaiThi = '';

    // Cấu hình Theo độ khó: [de, trung_binh, kho] tính theo %
    public int $doKhoDe        = 30;
    public int $doKhoTrungBinh = 50;
    public int $doKhoKho       = 20;

    // Cấu hình Tỷ lệ tùy chỉnh: mảng [chuong_id => phan_tram]
    public array $tyLeChuong = [];

    public function mount(): void
    {
        $monHoc = Subject::first();
        $this->monHocId  = $monHoc?->id ?? 0;
        $this->tenBaiThi = 'Bài thi ' . now()->format('d/m/Y H:i');

        if ($this->monHocId) {
            $this->khoiTaoTyLeChuong();
        }
    }

    public function updatedMonHocId(): void
    {
        $this->khoiTaoTyLeChuong();
    }

    public function updatedDoKhoDe(): void
    {
        $this->doKhoDe = max(0, min(100, (int) $this->doKhoDe));
    }

    public function updatedDoKhoTrungBinh(): void
    {
        $this->doKhoTrungBinh = max(0, min(100, (int) $this->doKhoTrungBinh));
    }

    public function updatedDoKhoKho(): void
    {
        $this->doKhoKho = max(0, min(100, (int) $this->doKhoKho));
    }

    private function khoiTaoTyLeChuong(): void
    {
        $chuongs = SubSubject::where('mon_hoc_id', $this->monHocId)
            ->orderBy('thu_tu')
            ->get();

        if ($chuongs->isEmpty()) {
            $this->tyLeChuong = [];
            return;
        }

        // Phân bổ đều
        $soLuong   = $chuongs->count();
        $phanTram  = (int) floor(100 / $soLuong);
        $du        = 100 - ($phanTram * $soLuong);

        $this->tyLeChuong = $chuongs->mapWithKeys(function ($c, $idx) use ($phanTram, $du) {
            return [$c->id => $phanTram + ($idx === 0 ? $du : 0)];
        })->toArray();
    }

    public function tongDoKho(): int
    {
        return $this->doKhoDe + $this->doKhoTrungBinh + $this->doKhoKho;
    }

    public function tongTyLe(): int
    {
        return array_sum($this->tyLeChuong);
    }

    public function batDauThi(MatrixGenerationService $matrix, ExamAttemptRepositoryInterface $attemptRepo): void
    {
        $this->validate();

        // Validate thêm tùy chế độ
        if ($this->cheDoThi === ExamMode::TheoDoKho->value) {
            if ($this->tongDoKho() !== 100) {
                $this->addError('doKho', 'Tổng tỷ lệ dễ/TB/khó phải bằng 100%.');
                return;
            }
        }

        if ($this->cheDoThi === ExamMode::TyLeTuyChon->value) {
            if (empty($this->tyLeChuong)) {
                $this->addError('tyLeChuong', 'Vui lòng cấu hình tỷ lệ chương.');
                return;
            }
            if ($this->tongTyLe() !== 100) {
                $this->addError('tyLeChuong', 'Tổng tỷ lệ các chương phải bằng 100%.');
                return;
            }
        }

        // Xây dựng cấu hình ma trận
        $cauHinhMaTran = null;

        if ($this->cheDoThi === ExamMode::TheoDoKho->value) {
            $cauHinhMaTran = [
                'de'         => $this->doKhoDe,
                'trung_binh' => $this->doKhoTrungBinh,
                'kho'        => $this->doKhoKho,
            ];
        } elseif ($this->cheDoThi === ExamMode::TyLeTuyChon->value) {
            $cauHinhMaTran = collect($this->tyLeChuong)
                ->filter(fn($pct) => $pct > 0)
                ->map(fn($pct, $chuongId) => ['chuong_id' => (int) $chuongId, 'phan_tram' => (int) $pct])
                ->values()
                ->toArray();
        }

        $baiThi = Exam::create([
            'nguoi_dung_id'    => Auth::id(),
            'mon_hoc_id'       => $this->monHocId,
            'ten_bai_thi'      => $this->tenBaiThi ?: 'Bài thi ' . now()->format('d/m H:i'),
            'che_do'           => $this->cheDoThi,
            'so_cau_hoi'       => $this->soCauHoi,
            'thoi_gian_phut'   => $this->thoiGianPhut,
            'cau_hinh_ma_tran' => $cauHinhMaTran,
        ]);

        try {
            $matrix->generate($baiThi);
        } catch (\InvalidArgumentException $e) {
            $baiThi->delete();
            $this->addError('monHocId', $e->getMessage());
            return;
        }

        $luotThi = $attemptRepo->taoLuotThi([
            'nguoi_dung_id' => Auth::id(),
            'bai_thi_id'    => $baiThi->id,
        ]);

        $this->redirect(route('exam.room', ['baiThi' => $baiThi->id, 'luot' => $luotThi->id]));
    }

    public function render()
    {
        $chuongs = $this->monHocId
            ? SubSubject::where('mon_hoc_id', $this->monHocId)->orderBy('thu_tu')->get()
            : collect();

        return view('livewire.student.create-exam', [
            'monHocs'  => Subject::orderBy('ten')->get(),
            'cheDoList'=> ExamMode::cases(),
            'chuongs'  => $chuongs,
        ]);
    }
}
