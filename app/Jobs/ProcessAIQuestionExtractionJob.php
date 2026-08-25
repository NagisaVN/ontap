<?php

namespace App\Jobs;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\GeminiAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessAIQuestionExtractionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;    // PDF lớn có thể mất 2-3 phút; retry sẽ chỉ lãng phí thời gian
    public int $timeout = 300; // 5 phút tối đa cho queue worker (Http timeout là 180s)

    /**
     * @param  string  $filePath       — Đường dẫn file trong Storage (VD: 'uploads/de_thi.pdf')
     * @param  int     $chuongId       — Chương mặc định để gán câu hỏi (có thể sửa sau)
     * @param  int     $nguoiUploadId  — Teacher đã upload (để notify)
     * @param  string  $mimeType       — MIME type của file
     */
    public function __construct(
        public readonly string $filePath,
        public readonly int    $chuongId,
        public readonly int    $nguoiUploadId,
        public readonly string $mimeType = 'application/pdf',
    ) {}

    public function handle(GeminiAIService $gemini): void
    {
        Log::info("[ProcessAIQuestionExtractionJob] Bắt đầu xử lý: {$this->filePath}");

        // 1. Đọc file và encode base64
        if (!Storage::exists($this->filePath)) {
            throw new \RuntimeException("File không tồn tại: {$this->filePath}");
        }

        $fileContent   = Storage::get($this->filePath);
        $base64Content = base64_encode($fileContent);

        // 2. Gọi Gemini Vision để trích xuất câu hỏi
        try {
            $danhSachCauHoi = $gemini->ocrTrichXuatCauHoi($base64Content, $this->mimeType);
        } catch (\Exception $e) {
            Log::error("[ProcessAIQuestionExtractionJob] Gemini API thất bại cho file {$this->filePath}: " . $e->getMessage(), [
                'exception_class' => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
            ]);
            // Re-throw để queue đánh dấu job là failed
            throw $e;
        }

        if (empty($danhSachCauHoi)) {
            Log::warning("[ProcessAIQuestionExtractionJob] AI không tìm thấy câu hỏi nào trong file.");
            return;
        }

        Log::info("[ProcessAIQuestionExtractionJob] AI tìm thấy " . count($danhSachCauHoi) . " câu hỏi.");

        // 3. Lưu vào DB — trang_thai = cho_duyet (teacher cần duyệt trước khi dùng)
        $soLuuThanh = 0;

        DB::transaction(function () use ($danhSachCauHoi, &$soLuuThanh) {
            foreach ($danhSachCauHoi as $index => $item) {
                try {
                    $this->luuMotCauHoi($item, $index);
                    $soLuuThanh++;
                } catch (Throwable $e) {
                    Log::warning("[ProcessAIQuestionExtractionJob] Lỗi câu #{$index}: {$e->getMessage()}", [
                        'item' => $item,
                    ]);
                }
            }
        });

        Log::info("[ProcessAIQuestionExtractionJob] Hoàn tất: {$soLuuThanh}/" . count($danhSachCauHoi) . " câu đã lưu (trạng thái: chờ duyệt).");

        // 4. TODO (STEP 4): Gửi notification cho teacher
        // event(new QuestionExtractionCompleted($this->nguoiUploadId, $soLuuThanh, $this->chuongId));
    }

    /**
     * Lưu một câu hỏi từ kết quả OCR vào DB.
     */
    private function luuMotCauHoi(array $item, int $index): void
    {
        // Validate cấu trúc tối thiểu
        if (empty($item['noi_dung']) || empty($item['lua_chon'])) {
            throw new \InvalidArgumentException("Câu #{$index} thiếu noi_dung hoặc lua_chon.");
        }

        $luaChon = $item['lua_chon'];
        if (count($luaChon) < 2) {
            throw new \InvalidArgumentException("Câu #{$index} phải có ít nhất 2 lựa chọn.");
        }

        // Normalize do_kho
        $doKho = in_array($item['do_kho'] ?? '', ['de', 'trung_binh', 'kho'])
            ? $item['do_kho']
            : 'trung_binh';

        // Tạo câu hỏi với trang_thai = cho_duyet
        $cauHoi = Question::create([
            'chuong_id'  => $this->chuongId,
            'noi_dung'   => trim($item['noi_dung']),
            'do_kho'     => $doKho,
            'giai_thich' => $item['giai_thich'] ?? null,
            'do_ai_tao'  => true,
            'trang_thai' => 'cho_duyet', // ← Bắt buộc qua teacher duyệt
            'nguon'      => 'ocr',
        ]);

        // Tạo các lựa chọn
        foreach ($luaChon as $thuTu => $lc) {
            QuestionOption::create([
                'cau_hoi_id' => $cauHoi->id,
                'noi_dung'   => trim($lc['noi_dung'] ?? "Lựa chọn " . ($thuTu + 1)),
                'la_dap_an'  => (bool) ($lc['la_dap_an'] ?? false),
                'thu_tu'     => $thuTu,
            ]);
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::error("[ProcessAIQuestionExtractionJob] Thất bại file {$this->filePath}", [
            'error'           => $exception->getMessage(),
            'exception_class' => get_class($exception),
            'file'            => $exception->getFile(),
            'line'            => $exception->getLine(),
        ]);
    }
}
