<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiAIService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private string $apiVersion;
    private int    $timeout;

    public function __construct()
    {
        $this->apiKey     = config('gemini.api_key', '');
        $this->model      = config('gemini.model', 'gemini-2.0-flash');
        $this->baseUrl    = rtrim(config('gemini.base_url'), '/');
        $this->apiVersion = config('gemini.api_version', 'v1');
        $this->timeout    = config('gemini.timeout', 90);
    }

    // ===================================================================
    // 1. OCR: Trích xuất câu hỏi từ PDF/Ảnh
    // ===================================================================

    /**
     * Gửi nội dung file (base64) lên Gemini Vision để trích xuất câu hỏi trắc nghiệm.
     *
     * @param  string  $base64Content  Nội dung file đã encode base64
     * @param  string  $mimeType       VD: 'image/jpeg', 'image/png', 'application/pdf'
     * @return array   Danh sách câu hỏi: [{noi_dung, lua_chon: [{noi_dung, la_dap_an}], do_kho, chuong_goi_y}]
     */
    public function ocrTrichXuatCauHoi(string $base64Content, string $mimeType): array
    {
        $prompt = <<<PROMPT
Bạn là chuyên gia phân tích đề thi. Hãy trích xuất TẤT CẢ câu hỏi trắc nghiệm từ tài liệu được cung cấp.

Yêu cầu output là JSON array CHÍNH XÁC theo cấu trúc sau (không có text nào khác ngoài JSON):
[
  {
    "noi_dung": "Nội dung câu hỏi đầy đủ",
    "lua_chon": [
      {"noi_dung": "Đáp án A", "la_dap_an": false},
      {"noi_dung": "Đáp án B", "la_dap_an": true},
      {"noi_dung": "Đáp án C", "la_dap_an": false},
      {"noi_dung": "Đáp án D", "la_dap_an": false}
    ],
    "do_kho": "de|trung_binh|kho",
    "giai_thich": "Giải thích ngắn tại sao đáp án đúng",
    "chuong_goi_y": "Tên chủ đề/chương liên quan"
  }
]

Quy tắc chung:
- Chỉ xuất JSON, không có markdown code fence, không có giải thích thêm
- la_dap_an: true cho đáp án ĐÚNG, false cho đáp án SAI
- Nếu không có đáp án đúng rõ ràng, đặt la_dap_an=false cho tất cả và thêm ghi chú vào noi_dung

═══ TIÊU CHÍ PHÂN LOẠI ĐỘ KHÓ (do_kho) — áp dụng nghiêm túc ═══

"de" — Câu NHẬN BIẾT / GHI NHỚ (tương đương Bloom cấp 1–2):
  • Hỏi thẳng định nghĩa, tên gọi, công thức, sự kiện ("X là gì?", "Công thức Y là?")
  • Học sinh chỉ cần nhớ, không cần suy luận hay tính toán
  • Các đáp án sai bị loại trừ ngay, không gây nhầm lẫn
  • Ví dụ điển hình:
      - "Subject + to be + V-ing là thì gì?"
      - "Thủ đô của Nhật Bản là?"
      - "Vitamin C có nhiều trong loại quả nào?"
      - "Hàm số f(x) = x² có đạo hàm là?"

"trung_binh" — Câu HIỂU / VẬN DỤNG (Bloom cấp 3–4):
  • Yêu cầu hiểu bản chất rồi áp dụng vào tình huống cụ thể
  • Có thể có bẫy ngữ pháp, ngữ nghĩa hoặc số liệu gây nhầm
  • Các đáp án sai trông hợp lý, cần đọc kỹ để loại trừ
  • Ví dụ điển hình:
      - "Chọn dạng đúng của động từ trong câu: She ___ here since 2020."
      - "Tính giá trị của biểu thức 3x² + 2x − 1 khi x = −2"
      - "Đoạn văn chủ yếu đề cập đến vấn đề gì?"
      - "Câu nào sau đây dùng đúng thì hiện tại hoàn thành?"

"kho" — Câu PHÂN TÍCH / ĐÁNH GIÁ / SUY LUẬN (Bloom cấp 5–6):
  • Phải kết hợp nhiều kiến thức hoặc suy luận nhiều bước
  • Nhiều đáp án gây nhầm lẫn cao, phải có lý luận để chọn
  • Hỏi về ý nghĩa ẩn, ngụ ý, hậu quả, so sánh phương án
  • Ví dụ điển hình:
      - "Tác giả dùng hình ảnh 'bóng tối' nhằm ám chỉ điều gì?"
      - "Phương pháp nào hiệu quả nhất để giải bài toán trên? Tại sao?"
      - Câu đọc hiểu yêu cầu suy ra thái độ/quan điểm của tác giả
      - Câu toán tích hợp nhiều công thức hoặc biến đổi đại số phức tạp

QUY TẮC TIE-BREAK khi phân vân:
  → Câu có BẪY hoặc cần VẬN DỤNG: chọn mức CAO HƠN
  → Câu chỉ hỏi thẳng một SỰ KIỆN hoặc ĐỊNH NGHĨA: chọn mức THẤP HƠN
PROMPT;

        $response = $this->guiYeuCauVision($prompt, $base64Content, $mimeType);
        return $this->parseJsonResponse($response, 'ocrTrichXuatCauHoi');
    }

    // ===================================================================
    // 2. Explainable AI: Giải thích câu trả lời sai
    // ===================================================================

    /**
     * Sinh giải thích chi tiết tại sao một đáp án là sai và đáp án nào mới là đúng.
     *
     * @param  string  $noiDungCauHoi
     * @param  string  $luaChonDaSai    Đáp án student đã chọn (sai)
     * @param  string  $luaChonDungDan  Đáp án đúng
     * @param  string  $monHoc          Tên môn học (context)
     * @return string  Giải thích dạng markdown
     */
    public function giaiThichCauHoi(
        string $noiDungCauHoi,
        string $luaChonDaSai,
        string $luaChonDungDan,
        string $monHoc = ''
    ): string {
        $prompt = <<<PROMPT
Bạn là giáo viên dạy môn {$monHoc}. Một học sinh vừa làm SAI một câu hỏi trắc nghiệm.

**Câu hỏi:** {$noiDungCauHoi}

**Đáp án học sinh chọn (SAI):** {$luaChonDaSai}

**Đáp án đúng:** {$luaChonDungDan}

Hãy viết giải thích ngắn gọn (tối đa 200 từ) theo cấu trúc:
1. **Tại sao "{$luaChonDaSai}" là bẫy?** (Học sinh thường nhầm vì...)
2. **Tại sao "{$luaChonDungDan}" mới là đúng?** (Vì...)
3. **Ghi nhớ:** (1 câu tóm tắt kiến thức cốt lõi)

Dùng tiếng Việt, ngắn gọn, dễ hiểu cho sinh viên.
PROMPT;

        return $this->guiYeuCauText($prompt);
    }

    // ===================================================================
    // 3. Adaptive: Sinh biến thể câu hỏi tương đương
    // ===================================================================

    /**
     * Sinh N câu hỏi biến thể từ một câu gốc.
     * Giữ nguyên khái niệm, thay đổi số liệu/ngữ cảnh/ví dụ.
     *
     * @param  string  $noiDungGoc       Nội dung câu hỏi gốc
     * @param  array   $luaChonGoc       [{noi_dung, la_dap_an}, ...]
     * @param  int     $soLuong          Số biến thể cần tạo (mặc định 3)
     * @return array   [{noi_dung, lua_chon: [{noi_dung, la_dap_an}]}]
     */
    public function sinhBienTheCauHoi(
        string $noiDungGoc,
        array  $luaChonGoc,
        int    $soLuong = 3
    ): array {
        $luaChonStr = collect($luaChonGoc)
            ->map(fn($lc, $i) => chr(65 + $i) . '. ' . $lc['noi_dung'] . ($lc['la_dap_an'] ? ' ✓' : ''))
            ->implode("\n");

        $prompt = <<<PROMPT
Bạn là chuyên gia ra đề thi. Dựa trên câu hỏi gốc sau, hãy tạo {$soLuong} câu hỏi biến thể.

**Câu hỏi gốc:**
{$noiDungGoc}

**Lựa chọn gốc:**
{$luaChonStr}

**Yêu cầu biến thể:**
- Kiểm tra CÙNG khái niệm/kiến thức
- Thay đổi: số liệu, tên biến, ví dụ thực tế, góc độ hỏi
- KHÔNG thay đổi độ khó
- Mỗi biến thể phải có 4 lựa chọn, đúng 1 đáp án

Xuất JSON array CHÍNH XÁC (không có text khác):
[
  {
    "noi_dung": "Nội dung câu hỏi biến thể",
    "lua_chon": [
      {"noi_dung": "A", "la_dap_an": false},
      {"noi_dung": "B", "la_dap_an": true},
      {"noi_dung": "C", "la_dap_an": false},
      {"noi_dung": "D", "la_dap_an": false}
    ]
  }
]
PROMPT;

        $response = $this->guiYeuCauText($prompt);
        return $this->parseJsonResponse($response, 'sinhBienTheCauHoi');
    }

    // ===================================================================
    // PRIVATE: HTTP helpers
    // ===================================================================

    /**
     * Gửi yêu cầu text-only tới Gemini API.
     */
    private function guiYeuCauText(string $prompt): string
    {
        $this->kiemTraApiKey();

        $url  = "{$this->baseUrl}/{$this->apiVersion}/models/{$this->model}:generateContent?key={$this->apiKey}";
        $body = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $prompt]]],
            ],
            'generationConfig' => config('gemini.generation_config'),
            'safetySettings'   => config('gemini.safety_settings'),
        ];

        return $this->thucHienRequest($url, $body);
    }

    /**
     * Gửi yêu cầu multimodal (text + file) tới Gemini API.
     */
    private function guiYeuCauVision(string $prompt, string $base64Content, string $mimeType): string
    {
        $this->kiemTraApiKey();

        $url  = "{$this->baseUrl}/{$this->apiVersion}/models/{$this->model}:generateContent?key={$this->apiKey}";
        $body = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data'     => $base64Content,
                            ],
                        ],
                    ],
                ],
            ],
            'generationConfig' => config('gemini.generation_config'),
            'safetySettings'   => config('gemini.safety_settings'),
        ];

        return $this->thucHienRequest($url, $body);
    }

    /**
     * Thực hiện HTTP POST tới Gemini API với retry tự động.
     *
     * CHÚ Ý retry:
     * - Retry chỉ khi bị rate-limit (429) hoặc lỗi server tạm thời (500, 503)
     * - KHÔNG retry ConnectionException (cURL timeout) vì làm mất gấp đôi thời gian
     */
    private function thucHienRequest(string $url, array $body): string
    {
        $retryConfig = config('gemini.retry');

        Log::debug("[GeminiAIService] POST {$url}");

        $response = Http::timeout($this->timeout)
            ->retry(
                $retryConfig['times'],
                $retryConfig['sleep'],
                function (\Exception $e, $request) {
                    // Chỉ retry khi bị rate-limit hoặc lỗi server, KHÔNG retry timeout
                    if ($e instanceof \Illuminate\Http\Client\ConnectionException) {
                        return false;
                    }
                    return $e instanceof \Illuminate\Http\Client\RequestException
                        && in_array($e->response->status(), [429, 500, 503]);
                }
            )
            ->post($url, $body);

        if ($response->failed()) {
            $status = $response->status();
            $error  = $response->json('error.message', 'Unknown error');
            Log::error("[GeminiAIService] API thất bại: HTTP {$status} — {$error}", [
                'url'         => $url,
                'model'       => $this->model,
                'api_version' => $this->apiVersion,
            ]);
            throw new RuntimeException("Gemini API lỗi {$status}: {$error}");
        }

        // Trích xuất text từ response
        $text = $response->json('candidates.0.content.parts.0.text', '');

        if (empty($text)) {
            // Log toàn bộ response để phát hiện thay đổi response structure từ Gemini
            $finishReason = $response->json('candidates.0.finishReason', 'UNKNOWN');
            Log::error("[GeminiAIService] Gemini trả về text rỗng", [
                'finishReason'   => $finishReason,
                'full_response'  => $response->json(),  // dump toàn bộ response structure
                'model'          => $this->model,
                'api_version'    => $this->apiVersion,
            ]);
            throw new RuntimeException("Gemini trả về nội dung trống. FinishReason: {$finishReason}");
        }

        return $text;
    }

    /**
     * Parse JSON từ response AI (loại bỏ markdown code fence nếu có).
     * Có 3 lớp bảo vệ trước khi từng lỗi JSON:
     *  1. Xóa code fence và ký tự kiểm soát (\r) gây "Control character error"
     *  2. Cố gắng vá JSON bị cắt giữa chừng (maxOutputTokens quá thấp)
     *  3. Nếu vẫn lỗi: log toàn bộ raw text để debug dễ hơn
     */
    private function parseJsonResponse(string $rawText, string $context): array
    {
        // Lớp 1: Xóa markdown code fence + ký tự kiểm soát
        $clean = preg_replace('/^```(?:json)?\s*|\s*```$/m', '', trim($rawText));
        $clean = str_replace(["\r\n", "\r"], "\n", $clean); // chuẩn hóa line endings
        $clean = trim($clean);

        $data = json_decode($clean, true);

        // Lớp 2: Nếu lỗi và output trông giống mảng bị cắt — thử “vá” lại
        if ((json_last_error() !== JSON_ERROR_NONE || !is_array($data)) && str_starts_with($clean, '[')) {
            // Tìm vị trí ”}“ cuối cùng để cắt bỏ phần incomplete
            $lastClose = strrpos($clean, '}');
            if ($lastClose !== false) {
                $salvaged = substr($clean, 0, $lastClose + 1) . ']';
                $data     = json_decode($salvaged, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    Log::warning("[GeminiAIService:{$context}] JSON bị cắt (maxOutputTokens?), đã vá thành công: " . count($data) . " câu hỏi");
                    return $data;
                }
            }
        }

        // Lớp 3: Vẫn lỗi — log đầy đủ và throw
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            Log::error("[GeminiAIService:{$context}] JSON parse thất bại", [
                'json_error'  => json_last_error_msg(),
                'raw_length'  => strlen($rawText),
                'raw_preview' => mb_substr($rawText, 0, 2000), // nhiều hơn để debug
            ]);
            throw new RuntimeException("Gemini trả về JSON không hợp lệ trong {$context}: " . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Kiểm tra API key đã được cấu hình chưa.
     */
    private function kiemTraApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'GEMINI_API_KEY chưa được cấu hình. Thêm vào file .env: GEMINI_API_KEY=your_key_here'
            );
        }
    }
}
