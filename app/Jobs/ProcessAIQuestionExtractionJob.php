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

    public int $tries   = 20;    // PDF lớn có thể bị rate limit 429 nhiều lần
    public int $timeout = 600; // 10 phút tối đa — cho phép tối đa ~5-6 lần gọi API liên tiếp

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

        // 2. Trích xuất câu hỏi bằng Gemini — dùng chunked extraction cho PDF nhiều câu
        $danhSachCauHoi   = [];
        $maxBatches       = 8;    // Tối đa 8 lần gọi API (~160+ câu/lần = >1000 câu)
        $minBatchSize     = 3;    // Nếu batch trả về ít hơn 3 câu → coi như hết tài liệu
        $cauHoiCuoiCung   = null; // Nội dung câu cuối đã lấy — dùng làm anchor cho batch kế
        $cauHoiCuoiCungPrev = ''; // Detect vòng lặp / ảo giác của Gemini

        for ($batch = 1; $batch <= $maxBatches; $batch++) {
            try {
                if ($batch === 1) {
                    // Lần đầu: trích xuất từ đầu tài liệu
                    Log::info("[ProcessAIQuestionExtractionJob] Batch #{$batch}: trích xuất ban đầu...");
                    $ketQuaBatch = $gemini->ocrTrichXuatCauHoi($base64Content, $this->mimeType);
                } else {
                    // Các lần sau: tiếp tục từ câu cuối cùng đã lấy
                    Log::info("[ProcessAIQuestionExtractionJob] Batch #{$batch}: tiếp tục sau \"{$cauHoiCuoiCung}\"...");
                    $ketQuaBatch = $gemini->ocrTrichXuatTiepTheo($base64Content, $this->mimeType, $cauHoiCuoiCung);
                }
            } catch (\Exception $e) {
                Log::error("[ProcessAIQuestionExtractionJob] Gemini API thất bại ở batch #{$batch}: " . $e->getMessage(), [
                    'exception_class' => get_class($e),
                    'file'            => $e->getFile(),
                    'line'            => $e->getLine(),
                ]);

                // Xử lý lỗi Rate Limit (429)
                $errorMsg = $e->getMessage();
                if (str_contains($errorMsg, '429') || str_contains($errorMsg, 'Too Many Requests') || str_contains($errorMsg, 'quota')) {
                    $delay = 60;
                    if (preg_match('/Please retry in ([\d\.]+)s/', $errorMsg, $matches)) {
                        $delay = (int) ceil((float) $matches[1]) + 5; // Cộng thêm 5s cho an toàn
                    }
                    Log::warning("[ProcessAIQuestionExtractionJob] API quá tải, ngủ {$delay} giây rồi thử lại batch #{$batch}...");
                    sleep($delay);
                    $batch--; // Lùi lại 1 bước để vòng for lặp lại batch này
                    continue; 
                }

                // Nếu batch đầu tiên lỗi (không phải 429) — throw để job thất bại hoàn toàn
                if ($batch === 1) {
                    throw $e;
                }
                
                Log::warning("[ProcessAIQuestionExtractionJob] Dừng chunked extraction sớm ở batch #{$batch} do lỗi. Đã có " . count($danhSachCauHoi) . " câu.");
                break;
            }

            $demBatch = count($ketQuaBatch);
            Log::info("[ProcessAIQuestionExtractionJob] Batch #{$batch}: nhận được {$demBatch} câu hỏi.");

            if ($demBatch === 0) {
                Log::info("[ProcessAIQuestionExtractionJob] Batch #{$batch} trả về 0 câu — đã hết tài liệu.");
                break;
            }

            $danhSachCauHoi = array_merge($danhSachCauHoi, $ketQuaBatch);
            $cauHoiCuoiCung = end($ketQuaBatch)['noi_dung'] ?? '';

            // Guard: Gemini bịa lại câu cũ (vòng lặp ảo giác)
            if ($cauHoiCuoiCung === $cauHoiCuoiCungPrev) {
                Log::warning("[ProcessAIQuestionExtractionJob] Phát hiện vòng lặp ảo giác ở batch #{$batch}, dừng lại.");
                break;
            }
            $cauHoiCuoiCungPrev = $cauHoiCuoiCung;

            // Nếu batch trả về ít hơn ngưỡng tối thiểu → có thể đã hết tài liệu
            if ($demBatch < $minBatchSize) {
                Log::info("[ProcessAIQuestionExtractionJob] Batch #{$batch} trả về {$demBatch} câu (<{$minBatchSize}) — coi như hết tài liệu.");
                break;
            }
        }

        Log::info("[ProcessAIQuestionExtractionJob] Tổng số câu từ tất cả batches: " . count($danhSachCauHoi));

        if (empty($danhSachCauHoi)) {
            Log::warning("[ProcessAIQuestionExtractionJob] AI không tìm thấy câu hỏi nào trong file.");
            \Illuminate\Support\Facades\Cache::put('ocr_job_status_' . md5($this->filePath), 'done', 3600);
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

        // 4. Báo cho Frontend biết Job đã xong
        \Illuminate\Support\Facades\Cache::put('ocr_job_status_' . md5($this->filePath), 'done', 3600);
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
            'noi_dung'   => $this->sanitizeNoiDung($item['noi_dung']),
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
                'noi_dung'   => $this->sanitizeNoiDung($lc['noi_dung'] ?? "Lựa chọn " . ($thuTu + 1)),
                'la_dap_an'  => (bool) ($lc['la_dap_an'] ?? false),
                'thu_tu'     => $thuTu,
            ]);
        }
    }

    /**
     * Xóa số thứ tự đầu dòng mà AI OCR thường giữ lại từ PDF.
     *
     * Xử lý các dạng:
     *   "41. Nội dung"  → "Nội dung"
     *   "3) Nội dung"   → "Nội dung"
     *   "2- Nội dung"   → "Nội dung"
     *   "A. Đáp án"      → "Đáp án"  (cho phương án có prefix A/B/C/D)
     */
    private function sanitizeNoiDung(string $text): string
    {
        // Xoá số thứ tự đầu dòng: "41. ", "3) ", "2- "
        $text = preg_replace('/^\s*\d+[\.\.\-\)]\s+/', '', trim($text));

        // Xoá prefix chữ cái của đáp án: "A. ", "B) ", "C- "
        $text = preg_replace('/^\s*[A-Da-d][\.\.\-\)]\s+/', '', $text);

        return trim($text);
    }

    public function failed(Throwable $exception): void
    {
        Log::error("[ProcessAIQuestionExtractionJob] Thất bại file {$this->filePath}", [
            'error'           => $exception->getMessage(),
            'exception_class' => get_class($exception),
            'file'            => $exception->getFile(),
            'line'            => $exception->getLine(),
        ]);
        
        // Báo cho Frontend biết Job bị lỗi
        \Illuminate\Support\Facades\Cache::put('ocr_job_status_' . md5($this->filePath), 'error', 3600);
    }
}
