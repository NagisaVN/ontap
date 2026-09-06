<?php

namespace App\Livewire\Admin;

use App\Models\Major;
use App\Models\Subject;
use App\Models\SubSubject;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TaxonomyManager extends Component
{
    // Cấu hình Modal Thêm/Sửa
    public bool $showModal = false;
    public string $modalMode = 'create'; // create / edit
    public string $nodeType = 'major'; // major / subject / subsubject
    public ?int $nodeId = null;
    public ?int $parentId = null; // dùng khi tạo subject/subsubject

    // Các field form
    public string $ten = '';
    public string $ma_mon = '';
    public string $mo_ta = '';

    public function getTree(): array
    {
        $majors = Major::with(['monHoc' => function ($q) {
            $q->orderBy('ten')->with(['chuong' => function ($q2) {
                $q2->orderBy('thu_tu');
            }]);
        }])->orderBy('ten')->get();

        $tree = [];
        foreach ($majors as $major) {
            $majorNode = [
                'id'       => $major->id,
                'name'     => $major->ten,
                'type'     => 'major',
                'children' => []
            ];
            foreach ($major->monHoc as $subject) {
                $subjectNode = [
                    'id'       => $subject->id,
                    'name'     => $subject->ten,
                    'type'     => 'subject',
                    'children' => []
                ];
                foreach ($subject->chuong as $sub) {
                    $subjectNode['children'][] = [
                        'id'   => $sub->id,
                        'name' => $sub->ten,
                        'type' => 'subsubject'
                    ];
                }
                $majorNode['children'][] = $subjectNode;
            }
            $tree[] = $majorNode;
        }
        return $tree;
    }

    public function createMajor(): void
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->nodeType  = 'major';
        $this->showModal = true;
    }

    public function createChild(string $type, int $parentId): void
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->nodeType  = $type;
        $this->parentId  = $parentId;
        $this->showModal = true;
    }

    public function editNode(string $type, int $id): void
    {
        $this->resetForm();
        $this->modalMode = 'edit';
        $this->nodeType  = $type;
        $this->nodeId    = $id;

        if ($type === 'major') {
            $record = Major::findOrFail($id);
            $this->ten = $record->ten;
            $this->mo_ta = $record->mo_ta ?? '';
        } elseif ($type === 'subject') {
            $record = Subject::findOrFail($id);
            $this->ten = $record->ten;
            $this->ma_mon = $record->ma_mon ?? '';
            $this->mo_ta = $record->mo_ta ?? '';
        } elseif ($type === 'subsubject') {
            $record = SubSubject::findOrFail($id);
            $this->ten = $record->ten;
        }

        $this->showModal = true;
    }

    public function deleteNode(string $type, int $id): void
    {
        if ($type === 'major') {
            $major = Major::findOrFail($id);
            if ($major->monHoc()->count() > 0) {
                session()->flash('error', 'Không thể xóa Khối ngành này vì đang chứa Môn học.');
                return;
            }
            $major->delete();
            session()->flash('status', 'Đã xóa Khối ngành.');
        } elseif ($type === 'subject') {
            $subject = Subject::findOrFail($id);
            if ($subject->chuong()->count() > 0) {
                session()->flash('error', 'Không thể xóa Môn học này vì đang chứa Chương.');
                return;
            }
            $subject->delete();
            session()->flash('status', 'Đã xóa Môn học.');
        } elseif ($type === 'subsubject') {
            $sub = SubSubject::findOrFail($id);
            if ($sub->cauHoi()->count() > 0) {
                session()->flash('error', 'Không thể xóa Chương này vì đang chứa Câu hỏi.');
                return;
            }
            $sub->delete();
            session()->flash('status', 'Đã xóa Chương.');
        }
    }

    public function save(): void
    {
        $rules = [
            'ten' => 'required|max:255',
        ];
        
        if ($this->nodeType === 'subject') {
            $rules['ma_mon'] = 'nullable|max:50';
        }

        $this->validate($rules, [
            'ten.required' => 'Vui lòng nhập tên.',
        ]);

        if ($this->modalMode === 'create') {
            if ($this->nodeType === 'major') {
                Major::create([
                    'ten'   => $this->ten,
                    'mo_ta' => $this->mo_ta
                ]);
            } elseif ($this->nodeType === 'subject') {
                Subject::create([
                    'nganh_id' => $this->parentId,
                    'ten'      => $this->ten,
                    'ma_mon'   => $this->ma_mon,
                    'mo_ta'    => $this->mo_ta
                ]);
            } elseif ($this->nodeType === 'subsubject') {
                $maxThuTu = SubSubject::where('mon_hoc_id', $this->parentId)->max('thu_tu') ?? 0;
                SubSubject::create([
                    'mon_hoc_id' => $this->parentId,
                    'ten'        => $this->ten,
                    'thu_tu'     => $maxThuTu + 1
                ]);
            }
            session()->flash('status', 'Thêm mới thành công!');
        } else {
            // Edit
            if ($this->nodeType === 'major') {
                Major::where('id', $this->nodeId)->update([
                    'ten'   => $this->ten,
                    'mo_ta' => $this->mo_ta
                ]);
            } elseif ($this->nodeType === 'subject') {
                Subject::where('id', $this->nodeId)->update([
                    'ten'      => $this->ten,
                    'ma_mon'   => $this->ma_mon,
                    'mo_ta'    => $this->mo_ta
                ]);
            } elseif ($this->nodeType === 'subsubject') {
                SubSubject::where('id', $this->nodeId)->update([
                    'ten' => $this->ten
                ]);
            }
            session()->flash('status', 'Cập nhật thành công!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->ten = '';
        $this->ma_mon = '';
        $this->mo_ta = '';
        $this->nodeId = null;
        $this->parentId = null;
        $this->resetValidation();
    }

    // Quick add via inline input
    public function addChild(string $type, int $parentId, string $name): void
    {
        $name = trim($name);
        if (blank($name)) return;

        if ($type === 'subject') {
            Subject::create([
                'nganh_id' => $parentId,
                'ten'      => $name,
            ]);
        } elseif ($type === 'subsubject') {
            $maxThuTu = SubSubject::where('mon_hoc_id', $parentId)->max('thu_tu') ?? 0;
            SubSubject::create([
                'mon_hoc_id' => $parentId,
                'ten'        => $name,
                'thu_tu'     => $maxThuTu + 1
            ]);
        }
        
        session()->flash('status', "Đã thêm nhanh '$name' thành công!");
    }

    public function render()
    {
        return view('livewire.admin.taxonomy-manager', [
            'tree' => $this->getTree(),
        ]);
    }
}
