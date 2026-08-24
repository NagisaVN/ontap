<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Câu hỏi chờ duyệt')]
class PendingReview extends Component
{
    use WithPagination;

    public array $selected = [];

    public function duyet(int $id): void
    {
        Question::findOrFail($id)->update(['trang_thai' => 'da_duyet']);
        session()->flash('success', 'Đã duyệt câu hỏi #'.$id);
    }

    public function tuChoi(int $id): void
    {
        Question::findOrFail($id)->update(['trang_thai' => 'tu_choi']);
        session()->flash('success', 'Đã từ chối câu hỏi #'.$id);
    }

    public function duyetHangLoat(): void
    {
        if (empty($this->selected)) return;

        Question::whereIn('id', $this->selected)
            ->where('trang_thai', 'cho_duyet')
            ->update(['trang_thai' => 'da_duyet']);

        $so = count($this->selected);
        $this->selected = [];
        session()->flash('success', "Đã duyệt {$so} câu hỏi.");
    }

    public function render()
    {
        $cauHois = Question::choDuyet()
            ->with(['chuong.monHoc', 'luaChon'])
            ->latest()
            ->paginate(10);

        return view('livewire.teacher.pending-review', compact('cauHois'));
    }
}
