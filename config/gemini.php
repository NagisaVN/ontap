<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình kết nối tới Google Gemini API.
    | Đặt GEMINI_API_KEY trong file .env.
    |
    */

    'api_key'  => env('GEMINI_API_KEY', ''),

    /*
     | Các model hiện đang hoạt động (2026):
     |  - gemini-3.6-flash          (khuyến nghị — nhanh, mạnh, bản mới nhất)
     |  - gemini-3.5-pro            (chính xác hơn cho logic phức tạp)
     */
    'model'    => env('GEMINI_MODEL', 'gemini-3.6-flash'),

    /*
     | API Version: 'v1' (stable) hoặc 'v1beta' (có tính năng thử nghiệm)
     | gemini-3-flash-preview chỉ hoạt động trên v1beta.
     */
    'api_version' => env('GEMINI_API_VERSION', 'v1beta'),

    'base_url' => 'https://generativelanguage.googleapis.com',

    // Timeout HTTP (giây) — 180s để xử lý PDF lớn nhiều câu hỏi
    'timeout'  => (int) env('GEMINI_TIMEOUT', 180),

    // Retry khi bị lỗi tạm thời (HTTP client retry)
    'retry' => [
        'times' => 1,
        'sleep' => 1000, // Thất bại nhanh để Job có thể xử lý release/delay thay vì block Worker
    ],

    // Safety settings — tắt filter để tránh block câu hỏi học thuật
    'safety_settings' => [
        ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
    ],

    // Generation config
    'generation_config' => [
        'temperature'     => 0.4,
        'topP'            => 0.95,
        'topK'            => 40,
        // Tăng token limit để tránh JSON bị cắt giữa chừng khi PDF lớn
        'maxOutputTokens' => 16384,
        // !! KHÔNG dùng 'responseMimeType' => 'application/json' ở đây !!
        // Lý do: khi set responseMimeType, Gemini KHÔNG trả text vào
        // candidates[0].content.parts[0].text mà thay đổi response structure,
        // khiến text extractor trả về '' và json_decode('') = null nhưng
        // json_last_error() = JSON_ERROR_NONE => lỗi "No error" khó debug.
        // JSON được đảm bảo sạch bằng prompt instruction + parseJsonResponse().
    ],
];
