<?php
/**
 * ============================================================
 * BÀI TEST 1: GenerateExplainableAIJob
 * Mô phỏng: Student làm sai câu hỏi → AI giải thích tại sao sai
 *
 * Chạy: php artisan tinker --execute="require base_path('test_ai_explain.php');"
 * Yêu cầu: GEMINI_API_KEY đã được set trong .env
 * ============================================================
 */

use App\Jobs\GenerateExplainableAIJob;
use App\Jobs\ProcessAIQuestionExtractionJob;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Major;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\SubSubject;
use App\Models\User;
use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use App\Services\ExamGradingService;
use App\Services\MatrixGenerationService;
use App\Enums\ExamMode;

echo "\n";
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  BÀI TEST 1: GenerateExplainableAIJob               ║\n";
echo "║  Giải thích AI cho câu trả lời sai                  ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

// ── Kiểm tra API Key ──────────────────────────────────────
$apiKey = config('gemini.api_key');
if (empty($apiKey)) {
    echo "❌ GEMINI_API_KEY chưa được set trong .env!\n";
    echo "   Thêm dòng sau vào .env:\n";
    echo "   GEMINI_API_KEY=your_actual_key_here\n\n";
    return;
}
echo "✅ GEMINI_API_KEY: " . substr($apiKey, 0, 8) . "...\n";
echo "✅ Model: " . config('gemini.model') . "\n\n";

// ── BƯỚC 1: Chuẩn bị dữ liệu mẫu ────────────────────────
echo "📦 [1] Chuẩn bị dữ liệu mẫu...\n";

// Kiểm tra đã có dữ liệu chưa (từ tinker_test.php)
$monHoc = Subject::first();
$student = User::where('email', 'student@smartprep.test')->first();

if (!$monHoc || !$student) {
    echo "⚠️  Chưa có dữ liệu. Đang tạo...\n";
    // Tạo dữ liệu nhanh
    $nganh  = Major::create(['ten' => 'CNTT Test', 'mo_ta' => 'Test']);
    $monHoc = Subject::create(['nganh_id' => $nganh->id, 'ten' => 'Môn Test AI', 'ma_mon' => 'AI_TEST_'.time()]);
    $chuong = SubSubject::create(['mon_hoc_id' => $monHoc->id, 'ten' => 'Chương Test', 'thu_tu' => 1]);

    $cq = Question::create([
        'chuong_id' => $chuong->id,
        'noi_dung'  => 'Trong SQL, mệnh đề nào dùng để lọc dữ liệu sau khi GROUP BY?',
        'do_kho'    => 'trung_binh',
        'trang_thai'=> 'da_duyet',
        'nguon'     => 'thu_cong',
    ]);

    $options = [
        ['noi_dung' => 'WHERE',  'la_dap_an' => false, 'thu_tu' => 0],
        ['noi_dung' => 'HAVING', 'la_dap_an' => true,  'thu_tu' => 1],
        ['noi_dung' => 'ORDER BY', 'la_dap_an' => false, 'thu_tu' => 2],
        ['noi_dung' => 'SELECT', 'la_dap_an' => false, 'thu_tu' => 3],
    ];
    foreach ($options as $opt) {
        QuestionOption::create(array_merge($opt, ['cau_hoi_id' => $cq->id]));
    }
} else {
    echo "✅ Dùng dữ liệu có sẵn: {$monHoc->ten}\n";
}

// ── BƯỚC 2: Tạo bài thi + lượt thi ──────────────────────
echo "\n📝 [2] Tạo bài thi và lượt thi...\n";

$cauHoi = Question::daDuyet()
    ->whereHas('chuong', fn($q) => $q->where('mon_hoc_id', $monHoc->id))
    ->with('luaChon')
    ->first();

if (!$cauHoi) {
    echo "❌ Không tìm thấy câu hỏi đã duyệt cho môn này.\n";
    return;
}

echo "   📌 Câu hỏi: " . mb_substr($cauHoi->noi_dung, 0, 70) . "...\n";

$baiThi = Exam::create([
    'nguoi_dung_id'  => $student->id,
    'mon_hoc_id'     => $monHoc->id,
    'ten_bai_thi'    => 'Test AI Explain - ' . now()->format('H:i:s'),
    'che_do'         => ExamMode::NgauNhien->value,
    'so_cau_hoi'     => 1,
    'thoi_gian_phut' => 5,
]);
$baiThi->cauHoi()->attach($cauHoi->id, ['thu_tu' => 1]);

// Tạo lượt thi
$attemptRepo = app(ExamAttemptRepositoryInterface::class);
$luotThi = $attemptRepo->taoLuotThi([
    'nguoi_dung_id' => $student->id,
    'bai_thi_id'    => $baiThi->id,
]);

// Chọn sai: lấy đáp án SAI đầu tiên
$dapAnSai = $cauHoi->luaChon->firstWhere('la_dap_an', false);
$dapAnDung = $cauHoi->luaChon->firstWhere('la_dap_an', true);

ExamAttemptAnswer::create([
    'luot_thi_id' => $luotThi->id,
    'cau_hoi_id'  => $cauHoi->id,
    'lua_chon_id' => $dapAnSai->id,  // ← Chọn SAI cố tình
    'dung_sai'    => false,
    'giai_thich_ai' => null,          // ← AI sẽ điền vào đây
]);

echo "   ✅ Lượt thi ID: #{$luotThi->id}\n";
echo "   ❌ Đã chọn sai: \"{$dapAnSai->noi_dung}\"\n";
echo "   ✅ Đáp án đúng: \"{$dapAnDung->noi_dung}\"\n";

// ── BƯỚC 3: Dispatch Job đồng bộ ─────────────────────────
echo "\n🤖 [3] Gọi GenerateExplainableAIJob (đồng bộ)...\n";
echo "   ⏳ Đang gọi Gemini API, vui lòng chờ...\n\n";

$thoiGianBatDau = microtime(true);

GenerateExplainableAIJob::dispatchSync($luotThi->id, [$cauHoi->id]);

$thoiGianXong = round(microtime(true) - $thoiGianBatDau, 2);

// ── BƯỚC 4: Hiển thị kết quả ─────────────────────────────
echo "⏱️  Thời gian xử lý: {$thoiGianXong}s\n\n";

$ketQua = ExamAttemptAnswer::where('luot_thi_id', $luotThi->id)
    ->where('cau_hoi_id', $cauHoi->id)
    ->first();

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  KẾT QUẢ AI GIẢI THÍCH                              ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

if ($ketQua && $ketQua->giai_thich_ai) {
    echo "✅ giai_thich_ai đã được điền!\n\n";
    echo "┌─ Câu hỏi: " . mb_substr($cauHoi->noi_dung, 0, 60) . "\n";
    echo "├─ Đã chọn (SAI): {$dapAnSai->noi_dung}\n";
    echo "├─ Đáp án đúng:   {$dapAnDung->noi_dung}\n";
    echo "└─ Giải thích AI:\n\n";
    echo $ketQua->giai_thich_ai . "\n";
} else {
    echo "❌ giai_thich_ai vẫn trống — Kiểm tra log: storage/logs/laravel.log\n";
}

echo "\n✅ BÀI TEST 1 HOÀN TẤT!\n\n";
