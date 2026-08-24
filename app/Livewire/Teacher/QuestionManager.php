<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use App\Models\Subject;
use App\Models\SubSubject;
use App\Models\QuestionOption;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Quản lý câu hỏi')]
class QuestionManager extends Component
{
    use WithPagination;

    public string $search    = '';
    public int    $monHocId  = 0;
    public int    $chuongId  = 0;
    public string $doKho     = '';
    public string $trangThai = '';

    // Modal tạo/sửa
    public bool   $showModal = false;
    public ?int   $editingId = null;

    public string $formNoiDung  = '';
    public int    $formChuongId = 0;
    public string $formDoKho    = 'trung_binh';
    public string $formGiaiThich= '';
    public array  $formLuaChon  = [
        ['noi_dung' => '', 'la_dap_an' => false],
        ['noi_dung' => '', 'la_dap_an' => false],
        ['noi_dung' => '', 'la_dap_an' => false],
        ['noi_dung' => '', 'la_dap_an' => false],
    ];

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedMonHocId(): void { $this->chuongId = 0; $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset('editingId','formNoiDung','formGiaiThich');
        $this->formDoKho   = 'trung_binh';
        $this->formLuaChon = array_fill(0, 4, ['noi_dung' => '', 'la_dap_an' => false]);
        $this->showModal   = true;
    }

    public function openEdit(int $id): void
    {
        $cq = Question::with('luaChon')->findOrFail($id);
        $this->editingId     = $id;
        $this->formNoiDung   = $cq->noi_dung;
        $this->formChuongId  = $cq->chuong_id;
        $this->formDoKho     = $cq->do_kho->value;
        $this->formGiaiThich = $cq->giai_thich ?? '';
        $this->formLuaChon   = $cq->luaChon->map(fn($lc) => [
            'noi_dung' => $lc->noi_dung, 'la_dap_an' => $lc->la_dap_an,
        ])->toArray();
        $this->showModal     = true;
    }

    public function luu(): void
    {
        $this->validate([
            'formNoiDung'         => 'required|string|min:10',
            'formChuongId'        => 'required|exists:chuong,id',
            'formDoKho'           => 'required|in:de,trung_binh,kho',
            'formLuaChon.*.noi_dung' => 'required|string',
        ]);

        // Đảm bảo có đúng 1 đáp án đúng
        $soDung = collect($this->formLuaChon)->where('la_dap_an', true)->count();
        if ($soDung !== 1) {
            $this->addError('formLuaChon', 'Phải có đúng 1 đáp án đúng.');
            return;
        }

        $data = [
            'chuong_id'  => $this->formChuongId,
            'noi_dung'   => $this->formNoiDung,
            'do_kho'     => $this->formDoKho,
            'giai_thich' => $this->formGiaiThich ?: null,
            'trang_thai' => 'da_duyet',
            'nguon'      => 'thu_cong',
        ];

        if ($this->editingId) {
            $cq = Question::findOrFail($this->editingId);
            $cq->update($data);
            $cq->luaChon()->delete();
        } else {
            $cq = Question::create($data);
        }

        foreach ($this->formLuaChon as $i => $lc) {
            QuestionOption::create([
                'cau_hoi_id' => $cq->id,
                'noi_dung'   => $lc['noi_dung'],
                'la_dap_an'  => $lc['la_dap_an'],
                'thu_tu'     => $i,
            ]);
        }

        $this->showModal = false;
        session()->flash('success', $this->editingId ? 'Đã cập nhật câu hỏi.' : 'Đã thêm câu hỏi mới.');
    }

    public function xoa(int $id): void
    {
        Question::findOrFail($id)->delete();
        session()->flash('success', 'Đã xóa câu hỏi.');
    }

    public function render()
    {
        $query = Question::with('chuong.monHoc')
            ->when($this->search, fn($q) => $q->where('noi_dung', 'like', '%'.$this->search.'%'))
            ->when($this->monHocId, fn($q) => $q->whereHas('chuong', fn($s) => $s->where('mon_hoc_id', $this->monHocId)))
            ->when($this->chuongId, fn($q) => $q->where('chuong_id', $this->chuongId))
            ->when($this->doKho, fn($q) => $q->where('do_kho', $this->doKho))
            ->when($this->trangThai, fn($q) => $q->where('trang_thai', $this->trangThai))
            ->latest();

        $monHocs   = Subject::orderBy('ten')->get();
        $chuongs   = $this->monHocId ? SubSubject::where('mon_hoc_id', $this->monHocId)->get() : collect();
        $allChuong = SubSubject::with('monHoc')->get();

        return view('livewire.teacher.question-manager', [
            'cauHois'   => $query->paginate(12),
            'monHocs'   => $monHocs,
            'chuongs'   => $chuongs,
            'allChuong' => $allChuong,
        ]);
    }
}
