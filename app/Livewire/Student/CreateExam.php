<?php

namespace App\Livewire\Student;

use App\Enums\ExamMode;
use App\Models\Exam;
use App\Models\Subject;
use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use App\Services\MatrixGenerationService;
use Illuminate\Support\Facades\Auth;
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
    public int $soCauHoi = 20;

    #[Validate('required|integer|min:5|max:120')]
    public int $thoiGianPhut = 30;

    public string $tenBaiThi = '';

    public function mount(): void
    {
        $monHoc = Subject::first();
        $this->monHocId   = $monHoc?->id ?? 0;
        $this->tenBaiThi  = 'Bài thi ' . now()->format('d/m/Y H:i');
    }

    public function batDauThi(MatrixGenerationService $matrix, ExamAttemptRepositoryInterface $attemptRepo): void
    {
        $this->validate();

        $baiThi = Exam::create([
            'nguoi_dung_id'  => Auth::id(),
            'mon_hoc_id'     => $this->monHocId,
            'ten_bai_thi'    => $this->tenBaiThi ?: 'Bài thi ' . now()->format('d/m H:i'),
            'che_do'         => $this->cheDoThi,
            'so_cau_hoi'     => $this->soCauHoi,
            'thoi_gian_phut' => $this->thoiGianPhut,
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
        return view('livewire.student.create-exam', [
            'monHocs'  => Subject::orderBy('ten')->get(),
            'cheDoList'=> ExamMode::cases(),
        ]);
    }
}
