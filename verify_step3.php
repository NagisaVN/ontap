<?php
use App\Services\GeminiAIService;

$g = app(GeminiAIService::class);
$ref = new ReflectionClass($g);

$getVal = function(string $name) use ($ref, $g) {
    $prop = $ref->getProperty($name);
    $prop->setAccessible(true);
    return $prop->getValue($g);
};

$base    = $getVal('baseUrl');
$version = $getVal('apiVersion');
$model   = $getVal('model');
$key     = $getVal('apiKey');

echo "\n=== GeminiAIService URL Verification ===\n";
echo "base_url    : " . $base    . "\n";
echo "api_version : " . $version . "\n";
echo "model       : " . $model   . "\n";
echo "api_key set : " . (!empty($key) ? "YES (" . substr($key, 0, 8) . "...)" : "NO — MISSING!") . "\n";
echo "\nFull URL:\n";
echo $base . "/" . $version . "/models/" . $model . ":generateContent?key=***\n";
echo "\n";

// Kiểm tra nếu model cũ vẫn đang bị dùng
if (str_contains($model, '1.5-flash') && !str_contains($model, 'latest')) {
    echo "⚠️  Model '{$model}' có thể đã bị deprecated.\n";
    echo "   Thêm GEMINI_MODEL=gemini-2.0-flash vào .env\n\n";
} else {
    echo "✅ Model OK\n\n";
}
