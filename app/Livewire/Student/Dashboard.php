<?php

namespace App\Livewire\Student;

use App\Models\Subject;
use App\Services\ProgressTrackingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public int $selectedMonHocId = 0;

    public function mount(): void
    {
        $monHoc = Subject::first();
        $this->selectedMonHocId = $monHoc?->id ?? 0;
    }

    public function render(ProgressTrackingService $progressService)
    {
        $user    = Auth::user();
        $monHocs = Subject::orderBy('ten')->get();

        $tongQuan  = $progressService->layTongQuanUser($user);
        $radarData = [];
        $cauHoiSai = collect(); // mặc định rỗng nếu chưa chọn môn

        if ($this->selectedMonHocId) {
            $monHoc = Subject::find($this->selectedMonHocId);
            if ($monHoc) {
                $raw = $progressService->layDuLieuRadar($user, $monHoc);
                $radarData = [
                    'categories' => collect($raw)->pluck('ten_chuong')->toArray(),
                    'series'     => collect($raw)->pluck('phan_tram')->toArray(),
                ];
                $cauHoiSai = $progressService->layDanhSachCauSai($user)->take(20);
            }
        }

        $luotThiGanDay = $user->luotThi()
            ->with('baiThi.monHoc')
            ->where('trang_thai', 'hoan_thanh')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.student.dashboard', compact(
            'tongQuan', 'monHocs', 'radarData', 'cauHoiSai', 'luotThiGanDay'
        ));
    }
}
