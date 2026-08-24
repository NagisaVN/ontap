<?php

namespace App\Livewire\Teacher;

use App\Jobs\ProcessAIQuestionExtractionJob;
use App\Models\SubSubject;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Upload OCR')]
class OcrUpload extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240')]
    public $file = null;

    #[Validate('required|exists:chuong,id')]
    public int $chuongId = 0;

    public bool   $uploading  = false;
    public bool   $dispatched = false;
    public string $message    = '';

    public function upload(): void
    {
        $this->validate();
        $this->uploading = true;

        $mime     = $this->file->getMimeType();
        $path     = $this->file->store('ocr-uploads', 'local');

        ProcessAIQuestionExtractionJob::dispatch(
            filePath:      $path,
            chuongId:      $this->chuongId,
            nguoiUploadId: Auth::id(),
            mimeType:      $mime,
        )->onQueue('ai');

        $this->file        = null;
        $this->uploading   = false;
        $this->dispatched  = true;
        $this->message     = 'File đang được AI xử lý. Câu hỏi sẽ xuất hiện ở trang "Chờ duyệt" sau vài phút.';
    }

    public function render()
    {
        return view('livewire.teacher.ocr-upload', [
            'monHocs' => Subject::with('chuong')->orderBy('ten')->get(),
        ]);
    }
}
