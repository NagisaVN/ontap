<?php

namespace App\Livewire\Teacher;

use App\Jobs\ProcessAIQuestionExtractionJob;
use App\Models\SubSubject;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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

    #[Validate('required|integer|min:1|exists:chuong,id')]
    public int $chuongId = 0;

    public bool   $uploading  = false;
    public bool   $dispatched = false;
    public string $message    = '';

    protected function messages(): array
    {
        return [
            'file.required'     => 'Vui lòng chọn file tài liệu.',
            'file.mimes'        => 'File phải là PDF, JPG, PNG hoặc WEBP.',
            'file.max'          => 'File không được vượt quá 10MB.',
            'chuongId.required' => 'Vui lòng chọn chương đích.',
            'chuongId.min'      => 'Vui lòng chọn chương đích.',
            'chuongId.exists'   => 'Chương được chọn không hợp lệ.',
        ];
    }

    public function guiChoAI(): void
    {
        try {
            $this->validate();

            $this->uploading = true;

            $mime = $this->file->getMimeType();
            $path = $this->file->store('ocr-uploads', 'local');

            ProcessAIQuestionExtractionJob::dispatch(
                filePath:      $path,
                chuongId:      $this->chuongId,
                nguoiUploadId: Auth::id(),
                mimeType:      $mime,
            )->onQueue('ai');

            $this->file       = null;
            $this->dispatched = true;
            $this->message    = 'File đang được AI xử lý. Câu hỏi sẽ xuất hiện ở trang "Chờ duyệt" sau vài phút.';
        } catch (ValidationException $e) {
            // Re-throw so Livewire can populate @error() directives in the view.
            throw $e;
        } catch (\Throwable $e) {
            $this->addError('file', 'Đã xảy ra lỗi khi xử lý file. Vui lòng thử lại.');
            throw $e;
        } finally {
            // Always reset the spinner — prevents the button from being stuck.
            $this->uploading = false;
        }
    }

    public function render()
    {
        return view('livewire.teacher.ocr-upload', [
            'monHocs' => Subject::with('chuong')->orderBy('ten')->get(),
        ]);
    }
}

