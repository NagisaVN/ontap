<?php

namespace App\Livewire\Admin;

use App\Models\Subject;
use App\Models\SubSubject;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Cấu trúc Đào tạo')]
class AdminTaxonomyManager extends Component
{
    // Danh sách
    public $subjects = [];
    
    // Trạng thái chọn Subject
    public $activeSubjectId = null;

    // --- Form Môn Học (Subject) ---
    public $subjectId = null;
    public $subjectTen = '';
    public $subjectMaMon = '';
    public $subjectMoTa = '';

    // --- Form Chương (SubSubject) ---
    public $chapterId = null;
    public $chapterTen = '';
    public $chapterThuTu = 1;

    public function mount()
    {
        $this->loadSubjects();
    }

    public function loadSubjects()
    {
        $this->subjects = Subject::with('chuong')->orderBy('ten')->get();
        
        // Nếu đang chọn một môn học nhưng nó bị xoá mất, reset activeSubjectId
        if ($this->activeSubjectId && !$this->subjects->contains('id', $this->activeSubjectId)) {
            $this->activeSubjectId = null;
        }
    }

    public function selectSubject($id)
    {
        $this->activeSubjectId = $id;
        $this->resetChapterForm();
    }

    // ==========================================
    // =            QUẢN LÝ MÔN HỌC             =
    // ==========================================

    public function editSubject($id)
    {
        $subject = Subject::find($id);
        if ($subject) {
            $this->subjectId = $subject->id;
            $this->subjectTen = $subject->ten;
            $this->subjectMaMon = $subject->ma_mon;
            $this->subjectMoTa = $subject->mo_ta;
        }
    }

    public function resetSubjectForm()
    {
        $this->subjectId = null;
        $this->subjectTen = '';
        $this->subjectMaMon = '';
        $this->subjectMoTa = '';
        $this->resetValidation();
    }

    public function saveSubject()
    {
        $this->validate([
            'subjectTen' => 'required|string|max:255',
            'subjectMaMon' => 'required|string|max:50|unique:mon_hoc,ma_mon,' . $this->subjectId,
            'subjectMoTa' => 'nullable|string',
        ], [
            'subjectTen.required' => 'Tên môn học không được để trống',
            'subjectMaMon.required' => 'Mã môn không được để trống',
            'subjectMaMon.unique' => 'Mã môn đã tồn tại',
        ]);

        if ($this->subjectId) {
            Subject::find($this->subjectId)?->update([
                'ten' => $this->subjectTen,
                'ma_mon' => $this->subjectMaMon,
                'mo_ta' => $this->subjectMoTa,
            ]);
        } else {
            Subject::create([
                'nganh_id' => 1, // Tạm fix cứng nganh_id theo thiết kế DB
                'ten' => $this->subjectTen,
                'ma_mon' => $this->subjectMaMon,
                'mo_ta' => $this->subjectMoTa,
            ]);
        }

        $this->loadSubjects();
        $this->resetSubjectForm();
        $this->dispatch('notify', message: 'Đã lưu môn học thành công!');
    }

    public function deleteSubject($id)
    {
        $subject = Subject::find($id);
        if ($subject) {
            // Xoá các chương liên quan trước
            $subject->chuong()->delete();
            $subject->delete();
            $this->loadSubjects();
            $this->dispatch('notify', message: 'Đã xoá môn học và các chương liên quan!');
        }
    }

    // ==========================================
    // =             QUẢN LÝ CHƯƠNG             =
    // ==========================================

    public function editChapter($id)
    {
        $chapter = SubSubject::find($id);
        if ($chapter) {
            $this->chapterId = $chapter->id;
            $this->chapterTen = $chapter->ten;
            $this->chapterThuTu = $chapter->thu_tu;
        }
    }

    public function resetChapterForm()
    {
        $this->chapterId = null;
        $this->chapterTen = '';
        
        // Tự động gợi ý thứ tự tiếp theo
        if ($this->activeSubjectId) {
            $maxOrder = SubSubject::where('mon_hoc_id', $this->activeSubjectId)->max('thu_tu');
            $this->chapterThuTu = ($maxOrder ?? 0) + 1;
        } else {
            $this->chapterThuTu = 1;
        }
        $this->resetValidation();
    }

    public function saveChapter()
    {
        if (!$this->activeSubjectId) return;

        $this->validate([
            'chapterTen' => 'required|string|max:255',
            'chapterThuTu' => 'required|integer|min:1',
        ], [
            'chapterTen.required' => 'Tên chương không được để trống',
            'chapterThuTu.required' => 'Thứ tự không hợp lệ',
        ]);

        if ($this->chapterId) {
            SubSubject::find($this->chapterId)?->update([
                'ten' => $this->chapterTen,
                'thu_tu' => $this->chapterThuTu,
            ]);
        } else {
            SubSubject::create([
                'mon_hoc_id' => $this->activeSubjectId,
                'ten' => $this->chapterTen,
                'thu_tu' => $this->chapterThuTu,
            ]);
        }

        $this->loadSubjects();
        $this->resetChapterForm();
        $this->dispatch('notify', message: 'Đã lưu chương thành công!');
    }

    public function deleteChapter($id)
    {
        SubSubject::find($id)?->delete();
        $this->loadSubjects();
        $this->dispatch('notify', message: 'Đã xoá chương!');
    }

    public function render()
    {
        return view('livewire.admin.admin-taxonomy-manager');
    }
}
