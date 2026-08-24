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
     | Các model hiện đang hoạt động (2025):
     |  - gemini-2.0-flash          (khuyến nghị — nhanh, miễn phí)
     |  - gemini-2.5-flash          (mới nhất)
     |  - gemini-1.5-flash-latest   (phiên bản 1.5 ổn định nhất)
     |  - gemini-1.5-pro-latest     (chính xác hơn, chậm hơn)
     */
    'model'    => env('GEMINI_MODEL', 'gemini-3-flash-preview'),

    /*
     | API Version: 'v1' (stable) hoặc 'v1beta' (có tính năng thử nghiệm)
     | gemini-3-flash-preview chỉ hoạt động trên v1beta.
     */
    'api_version' => env('GEMINI_API_VERSION', 'v1beta'),

    'base_url' => 'https://generativelanguage.googleapis.com',

    // Timeout HTTP (giây)
    'timeout'  => (int) env('GEMINI_TIMEOUT', 90),

    // Retry khi bị rate-limit (429)
    'retry' => [
        'times' => 3,
        'sleep' => 2000, // milliseconds
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
        'maxOutputTokens' => 8192,
    ],
];
