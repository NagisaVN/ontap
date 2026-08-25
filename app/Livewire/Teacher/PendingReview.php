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

    public array $selected         = [];
    public bool  $chonTatCa        = false;
    public bool  $chonTatCaToanBo  = false; // true khi đã chọn toàn bộ (vượt qua phân trang)

    /** Khi user bỏ chọn tay một câu — tự bỏ tick "Chọn tất cả" */
    public function updatedSelected(): void
    {
        $this->chonTatCa       = false;
        $this->chonTatCaToanBo = false;
    }

    /** Toggle "Chọn tất cả" — chỉ chọn các ID đang hiển thị trang hiện tại */
    public function toggleChonTatCa(): void
    {
        $this->chonTatCaToanBo = false;
        if ($this->chonTatCa) {
            $this->selected = Question::choDuyet()
                ->latest()
                ->paginate(10, ['*'], 'page', $this->getPage())
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    /** Chọn TOÀN BỘ — nạp tất cả ID vượt qua phân trang */
    public function chonTatCaToanBoAction(): void
    {
        $this->selected = Question::choDuyet()
            ->latest()
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
        $this->chonTatCaToanBo = true;
    }

    /** Bỏ chọn toàn bộ, quay lại trạng thái ban đầu */
    public function boChonTatCa(): void
    {
        $this->selected        = [];
        $this->chonTatCa       = false;
        $this->chonTatCaToanBo = false;
    }

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
        $this->selected        = [];
        $this->chonTatCa       = false;
        $this->chonTatCaToanBo = false;
        session()->flash('success', "Đã duyệt {$so} câu hỏi.");
    }

    public function tuChoiHangLoat(): void
    {
        if (empty($this->selected)) return;

        $so = count($this->selected);

        // Xóa hẳn khỏi DB — dữ liệu OCR rác không cần giữ lại
        Question::whereIn('id', $this->selected)->delete();

        $this->selected        = [];
        $this->chonTatCa       = false;
        $this->chonTatCaToanBo = false;
        session()->flash('success', "Đã từ chối và xóa {$so} câu hỏi rác.");
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
