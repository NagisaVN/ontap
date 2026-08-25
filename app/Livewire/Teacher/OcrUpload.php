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

    #[Validate('required|in:extract,generate')]
    public string $mode = 'extract';

    public int $soLuongDe = 4;
    public int $soLuongTrungBinh = 4;
    public int $soLuongKho = 2;

    public bool   $uploading  = false;
    public bool   $dispatched = false;
    public string $message    = '';
    public ?string $jobId     = null;

    protected function messages(): array
    {
        return [
            'file.required'     => 'Vui lòng chọn file tài liệu.',
            'file.mimes'        => 'File phải là PDF, JPG, PNG hoặc WEBP.',
            'file.max'          => 'File không được vượt quá 10MB.',
            'chuongId.required' => 'Vui lòng chọn chương đích.',
            'chuongId.min'      => 'Vui lòng chọn chương đích.',
            'chuongId.exists'   => 'Chương được chọn không hợp lệ.',
            'mode.in'           => 'Chế độ không hợp lệ.',
        ];
    }

    public function guiChoAI(): void
    {
        try {
            $this->validate();
            
            if ($this->mode === 'generate') {
                $this->soLuongDe = max(0, (int)$this->soLuongDe);
                $this->soLuongTrungBinh = max(0, (int)$this->soLuongTrungBinh);
                $this->soLuongKho = max(0, (int)$this->soLuongKho);
                
                $total = $this->soLuongDe + $this->soLuongTrungBinh + $this->soLuongKho;
                
                if ($total < 1) {
                    $this->addError('soLuongDe', 'Tổng số lượng câu hỏi phải lớn hơn 0.');
                    return;
                }
                
                if ($total > 50) {
                    $this->addError('soLuongDe', 'Tổng số lượng câu hỏi không được vượt quá 50.');
                    return;
                }
            }

            $this->uploading = true;

            $mime = $this->file->getMimeType();
            $path = $this->file->store('ocr-uploads', 'local');

            if ($this->mode === 'generate') {
                \App\Jobs\GenerateAIQuestionsFromDocJob::dispatch(
                    filePath:         $path,
                    chuongId:         $this->chuongId,
                    nguoiUploadId:    Auth::id(),
                    mimeType:         $mime,
                    soLuongDe:        $this->soLuongDe,
                    soLuongTrungBinh: $this->soLuongTrungBinh,
                    soLuongKho:       $this->soLuongKho,
                )->onQueue('ai');
            } else {
                ProcessAIQuestionExtractionJob::dispatch(
                    filePath:      $path,
                    chuongId:      $this->chuongId,
                    nguoiUploadId: Auth::id(),
                    mimeType:      $mime,
                )->onQueue('ai');
            }

            $this->jobId      = md5($path);
            $this->file       = null;
            $this->dispatched = true;
            $this->message    = $this->mode === 'generate' 
                                ? 'AI đang đọc tài liệu và biên soạn câu hỏi mới...' 
                                : 'File đang được AI xử lý trích xuất câu hỏi...';

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

    public function checkJobStatus()
    {
        if (!$this->jobId) return;

        $status = \Illuminate\Support\Facades\Cache::get('ocr_job_status_' . $this->jobId);

        if ($status === 'done') {
            $this->dispatch('ocr-job-done');
            $this->jobId = null;
        } elseif ($status === 'error') {
            $this->dispatch('ocr-job-error');
            $this->jobId = null;
        }
    }

    public function render()
    {
        return view('livewire.teacher.ocr-upload', [
            'monHocs' => Subject::with('chuong')->orderBy('ten')->get(),
        ]);
    }
}

