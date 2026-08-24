<?php
use Illuminate\Support\Facades\Http;

$apiKey = config('gemini.api_key');
$base   = 'https://generativelanguage.googleapis.com';

$body = [
    'contents' => [['role' => 'user', 'parts' => [['text' => 'Say "OK"']]]],
    'generationConfig' => ['maxOutputTokens' => 5],
];

// Chỉ test các model từ danh sách /models (bỏ qua những cái đã biết 404)
$toTest = [
    'gemini-3-flash-preview',
    'gemini-3.1-flash-lite',
    'gemini-3.1-flash-lite-preview',
    'gemini-2.5-flash-lite',
    'gemini-pro-latest',
    'gemini-flash-lite-latest',
    'gemini-3.1-pro-preview',
];

echo "\n=== Testing Gemini 3.x / 2.5-lite models ===\n";
foreach ($toTest as $model) {
    foreach (['v1beta', 'v1'] as $version) {
        $url = "{$base}/{$version}/models/{$model}:generateContent?key={$apiKey}";
        echo "  {$version}/{$model} ... ";
        try {
            $resp = Http::timeout(12)->post($url, $body);
            if ($resp->successful()) {
                $text = trim($resp->json('candidates.0.content.parts.0.text', ''));
                echo "✅ WORKS! \"{$text}\"\n\n";
                echo "Thêm vào .env:\n  GEMINI_MODEL={$model}\n  GEMINI_API_VERSION={$version}\n\n";
                exit(0);
            }
            echo "❌ {$resp->status()}\n";
        } catch (\Exception $e) {
            echo "⏱️ Timeout\n";
        }
        break; // Chỉ test v1beta trước
    }
}
echo "\nKhông tìm được model hoạt động. Kiểm tra:\n";
echo "1. API key có được bật Google AI Studio không?\n";
echo "2. Region restriction (VN có thể bị block một số model)\n";
echo "3. Thử key mới từ https://aistudio.google.com/apikey\n";
