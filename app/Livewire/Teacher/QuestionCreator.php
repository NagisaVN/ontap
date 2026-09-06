<?php

namespace App\Livewire\Teacher;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SubSubject;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class QuestionCreator extends Component
{
    public string $type         = 'mcq';
    public string $questionText = '';
    public array  $options      = ['', '', '', ''];
    public int    $correctIndex = 0;
    public string $subject      = '';
    public string $difficulty   = 'medium';

    public function setCorrect(int $index): void
    {
        $this->correctIndex = $index;
    }

    public function addOption(): void
    {
        if (count($this->options) < 6) {
            $this->options[] = '';
        }
    }

    public function removeOption(int $index): void
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);

        if ($this->correctIndex >= count($this->options)) {
            $this->correctIndex = 0;
        }
    }

    public function updatedType(): void
    {
        $this->correctIndex = 0;
        $this->options = $this->type === 'tf' ? ['Đúng', 'Sai'] : ['', '', '', ''];
    }

    public function save(): void
    {
        $this->validate([
            'questionText'  => 'required|min:5',
            'subject'       => 'required',
            'difficulty'    => 'required|in:de,trung_binh,kho',
            'options'       => 'required|array|min:2',
            'options.*'     => 'required|string|min:1',
        ], [
            'questionText.required' => 'Nội dung câu hỏi không được để trống.',
            'questionText.min'      => 'Nội dung câu hỏi ít nhất 5 ký tự.',
            'subject.required'      => 'Vui lòng chọn chương.',
            'difficulty.required'   => 'Vui lòng chọn độ khó.',
            'options.min'           => 'Cần ít nhất 2 lựa chọn.',
            'options.*.required'    => 'Nội dung lựa chọn không được để trống.',
        ]);

        DB::transaction(function () {
            $question = Question::create([
                'noi_dung'      => $this->questionText,
                'chuong_id'     => $this->subject,
                'do_kho'        => $this->difficulty,
                'trang_thai'    => 'cho_duyet',
                'nguoi_dung_id' => auth()->id(),
            ]);

            foreach ($this->options as $i => $text) {
                QuestionOption::create([
                    'cau_hoi_id' => $question->id,
                    'noi_dung'   => $text,
                    'la_dap_an'  => ($i === $this->correctIndex),
                    'thu_tu'     => $i + 1,
                ]);
            }
        });

        session()->flash('status', 'Câu hỏi đã được tạo thành công và đang chờ duyệt!');
        $this->reset(['questionText', 'correctIndex']);
        $this->options = ['', '', '', ''];
        $this->type = 'mcq';
    }

    public function render()
    {
        $chuongs = SubSubject::with('monHoc')
            ->orderBy('mon_hoc_id')
            ->orderBy('thu_tu')
            ->get();

        return view('livewire.teacher.question-creator', compact('chuongs'));
    }
}
