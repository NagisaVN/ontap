<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\SubSubject;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class QuestionManager extends Component
{
    use WithPagination;

    // ── Filters & Selection ──────────────────────────────────────────
    public string $search            = '';
    public string $filterSubject     = '';
    public string $filterDifficulty  = '';
    public array  $selectedIds       = [];
    public bool   $selectAll         = false;

    // ── Edit Modal State ─────────────────────────────────────────────
    public bool   $showEditModal  = false;
    public ?int   $editingId      = null;

    // Form fields
    public string $editNoiDung    = '';
    public string $editDoKho      = 'de';
    public ?int   $editChuongId   = null;
    public string $editTrangThai  = 'cho_duyet';
    public array  $editLuaChon   = [];   // [{id|null, noi_dung, la_dap_an}]

    // ── Filter hooks ────────────────────────────────────────────────
    public function updatedSearch(): void           { $this->resetPage(); $this->selectedIds = []; $this->selectAll = false; }
    public function updatedFilterSubject(): void    { $this->resetPage(); $this->selectedIds = []; $this->selectAll = false; }
    public function updatedFilterDifficulty(): void { $this->resetPage(); $this->selectedIds = []; $this->selectAll = false; }

    public function updatedSelectAll(bool $value): void
    {
        $this->selectedIds = $value ? $this->getCurrentPageIds() : [];
    }

    public function updatedSelectedIds(): void
    {
        $pageIds = $this->getCurrentPageIds();
        $this->selectAll = !empty($pageIds) && empty(array_diff($pageIds, $this->selectedIds));
    }

    protected function getCurrentPageIds(): array
    {
        return $this->buildQuery()->paginate(10)->pluck('id')->map(fn($id) => (string) $id)->toArray();
    }

    // ── Delete ───────────────────────────────────────────────────────
    public function deleteQuestion(int $id): void
    {
        $question = Question::findOrFail($id);
        $question->delete(); // LogsActivity trait fires here automatically
        $this->selectedIds = array_values(array_filter($this->selectedIds, fn($i) => (int)$i !== $id));
        session()->flash('status', 'Da xoa cau hoi #' . $id);
    }

    public function deleteSelected(): void
    {
        if (empty($this->selectedIds)) return;
        // Fetch then delete individually so LogsActivity fires for each record
        Question::whereIn('id', $this->selectedIds)->get()->each->delete();
        $count = count($this->selectedIds);
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->resetPage();
        session()->flash('status', "Da xoa {$count} cau hoi.");
    }

    public function deleteAll(): void
    {
        $questions = $this->buildQuery()->get();
        $count = $questions->count();
        // Delete each individually so LogsActivity trait fires, then log one summary entry
        $questions->each->delete();
        activity()
            ->causedBy(auth()->user())
            ->withProperties(['count' => $count])
            ->log("Bulk deleted {$count} questions from question bank");
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->resetPage();
        session()->flash('status', "Da xoa tat ca {$count} cau hoi.");
    }

    // ── Edit Modal ───────────────────────────────────────────────────

    public function openEdit(int $id): void
    {
        $question = Question::with('luaChon')->findOrFail($id);

        $this->editingId     = $id;
        $this->editNoiDung   = $question->noi_dung;
        $this->editDoKho     = $question->do_kho?->value ?? 'de';
        $this->editChuongId  = $question->chuong_id;
        $this->editTrangThai = $question->trang_thai?->value ?? 'cho_duyet';

        // Load lựa chọn
        $this->editLuaChon = $question->luaChon->map(fn($lc) => [
            'id'       => $lc->id,
            'noi_dung' => $lc->noi_dung,
            'la_dap_an'=> (bool) $lc->la_dap_an,
            'thu_tu'   => $lc->thu_tu,
        ])->values()->toArray();

        // Đảm bảo ít nhất 4 lựa chọn
        while (count($this->editLuaChon) < 4) {
            $this->editLuaChon[] = ['id' => null, 'noi_dung' => '', 'la_dap_an' => false, 'thu_tu' => count($this->editLuaChon) + 1];
        }

        $this->showEditModal = true;
    }

    public function closeEdit(): void
    {
        $this->showEditModal = false;
        $this->editingId     = null;
        $this->editLuaChon  = [];
        $this->resetErrorBag();
    }

    public function setCorrectAnswer(int $index): void
    {
        foreach ($this->editLuaChon as $i => $lc) {
            $this->editLuaChon[$i]['la_dap_an'] = ($i === $index);
        }
    }

    public function addOption(): void
    {
        if (count($this->editLuaChon) < 6) {
            $this->editLuaChon[] = [
                'id'       => null,
                'noi_dung' => '',
                'la_dap_an'=> false,
                'thu_tu'   => count($this->editLuaChon) + 1,
            ];
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->editLuaChon) <= 2) return; // Tối thiểu 2 lựa chọn

        // Nếu xóa đáp án đúng → đặt câu đầu tiên là đúng
        $wasCorrect = $this->editLuaChon[$index]['la_dap_an'] ?? false;
        unset($this->editLuaChon[$index]);
        $this->editLuaChon = array_values($this->editLuaChon);

        if ($wasCorrect && count($this->editLuaChon) > 0) {
            $this->editLuaChon[0]['la_dap_an'] = true;
        }

        // Cập nhật thu_tu
        foreach ($this->editLuaChon as $i => $lc) {
            $this->editLuaChon[$i]['thu_tu'] = $i + 1;
        }
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editNoiDung'             => 'required|min:5',
            'editDoKho'               => 'required|in:de,trung_binh,kho',
            'editChuongId'            => 'required|exists:chuong,id',
            'editLuaChon'             => 'required|array|min:2',
            'editLuaChon.*.noi_dung'  => 'required|string|min:1',
        ], [
            'editNoiDung.required'            => 'Nội dung câu hỏi không được để trống.',
            'editNoiDung.min'                 => 'Nội dung câu hỏi ít nhất 5 ký tự.',
            'editChuongId.required'           => 'Vui lòng chọn chương.',
            'editChuongId.exists'             => 'Chương không hợp lệ.',
            'editLuaChon.min'                 => 'Cần ít nhất 2 lựa chọn.',
            'editLuaChon.*.noi_dung.required' => 'Nội dung lựa chọn không được để trống.',
        ]);

        // Phải có ít nhất 1 đáp án đúng
        $hasCorrect = collect($this->editLuaChon)->contains('la_dap_an', true);
        if (!$hasCorrect) {
            $this->addError('editLuaChon', 'Vui lòng chọn ít nhất 1 đáp án đúng.');
            return;
        }

        DB::transaction(function () {
            $question = Question::findOrFail($this->editingId);
            $question->update([
                'noi_dung'   => $this->editNoiDung,
                'do_kho'     => $this->editDoKho,
                'chuong_id'  => $this->editChuongId,
                'trang_thai' => $this->editTrangThai,
            ]);

            // Sync lựa chọn
            $existingIds = $question->luaChon->pluck('id')->toArray();
            $keepIds     = [];

            foreach ($this->editLuaChon as $i => $lc) {
                if (!empty($lc['id']) && in_array($lc['id'], $existingIds)) {
                    // Update existing
                    QuestionOption::where('id', $lc['id'])->update([
                        'noi_dung'   => $lc['noi_dung'],
                        'la_dap_an'  => $lc['la_dap_an'],
                        'thu_tu'     => $i + 1,
                    ]);
                    $keepIds[] = $lc['id'];
                } else {
                    // Create new
                    $new = QuestionOption::create([
                        'cau_hoi_id' => $question->id,
                        'noi_dung'   => $lc['noi_dung'],
                        'la_dap_an'  => $lc['la_dap_an'],
                        'thu_tu'     => $i + 1,
                    ]);
                    $keepIds[] = $new->id;
                }
            }

            // Xóa lựa chọn bị remove
            $deleteIds = array_diff($existingIds, $keepIds);
            if (!empty($deleteIds)) {
                QuestionOption::whereIn('id', $deleteIds)->delete();
            }
        });

        $this->closeEdit();
        session()->flash('status', 'Đã cập nhật câu hỏi #' . $this->editingId . ' thành công!');
    }

    // ── Query Builder ────────────────────────────────────────────────
    protected function buildQuery()
    {
        return Question::with(['chuong.monHoc'])
            ->when($this->search, fn($q) =>
                $q->where('noi_dung', 'like', '%' . $this->search . '%')
            )
            ->when($this->filterDifficulty, fn($q) =>
                $q->where('do_kho', $this->filterDifficulty)
            )
            ->when($this->filterSubject, fn($q) =>
                $q->whereHas('chuong.monHoc', fn($s) =>
                    $s->where('ten', $this->filterSubject)
                )
            )
            ->latest();
    }

    public function render()
    {
        $questions = $this->buildQuery()->paginate(10);
        $subjects  = Subject::orderBy('ten')->pluck('ten')->toArray();
        $chuongs   = SubSubject::with('monHoc')->orderBy('mon_hoc_id')->orderBy('thu_tu')->get();
        $total     = Question::count();

        return view('livewire.teacher.question-manager', compact('questions', 'subjects', 'chuongs', 'total'));
    }
}
