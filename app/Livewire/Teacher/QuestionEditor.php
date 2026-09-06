<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SubSubject;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuestionEditor extends Component
{
    public int    $questionId;
    public string $noiDung   = '';
    public string $doKho     = 'de';
    public ?int   $chuongId  = null;
    public string $trangThai = 'cho_duyet';
    public array  $luaChon   = [];

    public function mount(int $id): void
    {
        $question = Question::with('luaChon')->findOrFail($id);

        // Gate: teacher chỉ sửa câu hỏi của mình (nếu muốn giới hạn)
        // abort_unless($question->nguoi_dung_id === auth()->id(), 403);

        $this->questionId = $id;
        $this->noiDung    = $question->noi_dung;
        $this->doKho      = $question->do_kho?->value ?? 'de';
        $this->chuongId   = $question->chuong_id;
        $this->trangThai  = $question->trang_thai?->value ?? 'cho_duyet';

        $this->luaChon = $question->luaChon
            ->sortBy('thu_tu')
            ->values()
            ->map(fn($lc) => [
                'id'        => $lc->id,
                'noi_dung'  => $lc->noi_dung,
                'la_dap_an' => (bool) $lc->la_dap_an,
                'thu_tu'    => $lc->thu_tu,
            ])->toArray();

        // Đảm bảo ít nhất 4 lựa chọn
        while (count($this->luaChon) < 4) {
            $this->luaChon[] = [
                'id'        => null,
                'noi_dung'  => '',
                'la_dap_an' => false,
                'thu_tu'    => count($this->luaChon) + 1,
            ];
        }
    }

    public function setCorrectAnswer(int $index): void
    {
        foreach ($this->luaChon as $i => $lc) {
            $this->luaChon[$i]['la_dap_an'] = ($i === $index);
        }
    }

    public function addOption(): void
    {
        if (count($this->luaChon) < 6) {
            $this->luaChon[] = [
                'id'        => null,
                'noi_dung'  => '',
                'la_dap_an' => false,
                'thu_tu'    => count($this->luaChon) + 1,
            ];
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->luaChon) <= 2) return;

        $wasCorrect = $this->luaChon[$index]['la_dap_an'] ?? false;
        unset($this->luaChon[$index]);
        $this->luaChon = array_values($this->luaChon);

        if ($wasCorrect && !empty($this->luaChon)) {
            $this->luaChon[0]['la_dap_an'] = true;
        }

        foreach ($this->luaChon as $i => $_) {
            $this->luaChon[$i]['thu_tu'] = $i + 1;
        }
    }

    public function save(): void
    {
        $this->validate([
            'noiDung'              => 'required|min:5',
            'doKho'                => 'required|in:de,trung_binh,kho',
            'chuongId'             => 'required|exists:chuong,id',
            'luaChon'              => 'required|array|min:2',
            'luaChon.*.noi_dung'   => 'required|string|min:1',
        ], [
            'noiDung.required'           => 'Nội dung câu hỏi không được để trống.',
            'noiDung.min'                => 'Nội dung câu hỏi ít nhất 5 ký tự.',
            'chuongId.required'          => 'Vui lòng chọn chương.',
            'chuongId.exists'            => 'Chương không hợp lệ.',
            'luaChon.min'                => 'Cần ít nhất 2 lựa chọn.',
            'luaChon.*.noi_dung.required'=> 'Nội dung lựa chọn không được để trống.',
        ]);

        $hasCorrect = collect($this->luaChon)->contains('la_dap_an', true);
        if (!$hasCorrect) {
            $this->addError('luaChon', 'Vui lòng chọn ít nhất 1 đáp án đúng.');
            return;
        }

        DB::transaction(function () {
            $question = Question::findOrFail($this->questionId);
            $question->update([
                'noi_dung'   => $this->noiDung,
                'do_kho'     => $this->doKho,
                'chuong_id'  => $this->chuongId,
                'trang_thai' => $this->trangThai,
            ]);

            $existingIds = $question->luaChon->pluck('id')->toArray();
            $keepIds     = [];

            foreach ($this->luaChon as $i => $lc) {
                if (!empty($lc['id']) && in_array($lc['id'], $existingIds)) {
                    QuestionOption::where('id', $lc['id'])->update([
                        'noi_dung'  => $lc['noi_dung'],
                        'la_dap_an' => $lc['la_dap_an'],
                        'thu_tu'    => $i + 1,
                    ]);
                    $keepIds[] = $lc['id'];
                } else {
                    $new = QuestionOption::create([
                        'cau_hoi_id' => $question->id,
                        'noi_dung'   => $lc['noi_dung'],
                        'la_dap_an'  => $lc['la_dap_an'],
                        'thu_tu'     => $i + 1,
                    ]);
                    $keepIds[] = $new->id;
                }
            }

            $deleteIds = array_diff($existingIds, $keepIds);
            if (!empty($deleteIds)) {
                QuestionOption::whereIn('id', $deleteIds)->delete();
            }
        });

        session()->flash('status', 'Câu hỏi #' . $this->questionId . ' đã được cập nhật thành công!');
        $this->redirect(route('teacher.questions'), navigate: true);
    }

    public function render()
    {
        $chuongs = SubSubject::with('monHoc')
            ->orderBy('mon_hoc_id')
            ->orderBy('thu_tu')
            ->get();

        return view('livewire.teacher.question-editor', compact('chuongs'));
    }
}
