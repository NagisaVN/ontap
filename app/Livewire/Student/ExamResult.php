<?php

namespace App\Livewire\Student;

use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Kết quả bài thi')]
class ExamResult extends Component
{
    public ExamAttempt $luotThi;

    public function mount(ExamAttempt $luotThi): void
    {
        // Bảo mật: chỉ xem kết quả của chính mình
        abort_unless($luotThi->nguoi_dung_id === Auth::id(), 403);

        $luotThi->load([
            'baiThi.monHoc',
            'ketQua.cauHoi.luaChon',
            'ketQua.luaChonDaChon',
        ]);

        $this->luotThi = $luotThi;
    }

    public function render()
    {
        $baiThi   = $this->luotThi->baiThi;
        $tongCau  = $this->luotThi->ketQua->count();
        $soDung   = $this->luotThi->so_cau_dung;
        $soSai    = $tongCau - $soDung;
        $soBo     = $this->luotThi->ketQua->whereNull('lua_chon_id')->count();

        $rank = match(true) {
            $this->luotThi->diem_so >= 9  => ['label' => 'Xuất sắc', 'color' => 'green', 'emoji' => '🏆'],
            $this->luotThi->diem_so >= 7  => ['label' => 'Giỏi',     'color' => 'indigo','emoji' => '🌟'],
            $this->luotThi->diem_so >= 5  => ['label' => 'Đạt',      'color' => 'yellow','emoji' => '👍'],
            default                        => ['label' => 'Cần cố gắng', 'color' => 'red', 'emoji' => '💪'],
        };

        return view('livewire.student.exam-result', compact(
            'baiThi', 'tongCau', 'soDung', 'soSai', 'soBo', 'rank'
        ));
    }
}
