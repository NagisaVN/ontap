<?php
/**
 * ============================================================
 * BÀI TEST 2: AdaptiveQuestionSynthesisJob
 * Mô phỏng: Student sai >= 3 lần → AI sinh 3 câu biến thể
 *
 * Chạy: php artisan tinker --execute="require base_path('test_ai_adaptive.php');"
 * Yêu cầu: GEMINI_API_KEY đã được set trong .env
 * ============================================================
 */

use App\Jobs\AdaptiveQuestionSynthesisJob;
use App\Models\Major;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\SubSubject;
use App\Models\User;
use App\Models\UserQuestionStat;

echo "\n";
echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  BÀI TEST 2: AdaptiveQuestionSynthesisJob           ║\n";
echo "║  AI tự sinh câu hỏi biến thể khi sai >= 3 lần      ║\n";
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

// ── BƯỚC 1: Chuẩn bị câu hỏi gốc ────────────────────────
echo "📦 [1] Chuẩn bị câu hỏi gốc để sinh biến thể...\n";

$student = User::where('email', 'student@smartprep.test')->first();
$cauHoiGoc = Question::daDuyet()->with('luaChon')->first();

if (!$cauHoiGoc) {
    echo "⚠️  Không có câu hỏi. Đang tạo câu hỏi mẫu...\n";

    $nganh  = Major::first() ?? Major::create(['ten' => 'CNTT', 'mo_ta' => 'Test']);
    $monHoc = Subject::first() ?? Subject::create([
        'nganh_id' => $nganh->id, 'ten' => 'Toán rời rạc',
        'ma_mon' => 'TRR_' . time(),
    ]);
    $chuong = SubSubject::first() ?? SubSubject::create([
        'mon_hoc_id' => $monHoc->id, 'ten' => 'Đồ thị', 'thu_tu' => 1,
    ]);

    $cauHoiGoc = Question::create([
        'chuong_id'  => $chuong->id,
        'noi_dung'   => 'Cho đồ thị G có 5 đỉnh và 7 cạnh. Số cạnh tối đa của một đồ thị vô hướng đơn có 5 đỉnh là bao nhiêu?',
        'do_kho'     => 'trung_binh',
        'giai_thich' => 'Số cạnh tối đa = n*(n-1)/2 = 5*4/2 = 10 cạnh.',
        'trang_thai' => 'da_duyet',
        'nguon'      => 'thu_cong',
    ]);

    $opts = [
        ['noi_dung' => '8 cạnh',  'la_dap_an' => false, 'thu_tu' => 0],
        ['noi_dung' => '10 cạnh', 'la_dap_an' => true,  'thu_tu' => 1],
        ['noi_dung' => '12 cạnh', 'la_dap_an' => false, 'thu_tu' => 2],
        ['noi_dung' => '14 cạnh', 'la_dap_an' => false, 'thu_tu' => 3],
    ];
    foreach ($opts as $opt) {
        QuestionOption::create(array_merge($opt, ['cau_hoi_id' => $cauHoiGoc->id]));
    }
    $cauHoiGoc->load('luaChon');
}

echo "   📌 Câu hỏi gốc (ID: #{$cauHoiGoc->id}):\n";
echo "      \"" . mb_substr($cauHoiGoc->noi_dung, 0, 80) . "\"\n";
echo "   🔢 Độ khó: {$cauHoiGoc->do_kho->nhanHien()}\n";
echo "   📋 Lựa chọn:\n";
foreach ($cauHoiGoc->luaChon as $lc) {
    $mark = $lc->la_dap_an ? ' ✅' : '';
    echo "      " . chr(65 + $lc->thu_tu) . ". {$lc->noi_dung}{$mark}\n";
}

// ── BƯỚC 2: Giả lập sai >= 3 lần ─────────────────────────
echo "\n⚠️  [2] Giả lập student sai câu này 3 lần (kích hoạt Adaptive AI)...\n";

$thongKe = UserQuestionStat::updateOrCreate(
    ['nguoi_dung_id' => $student->id, 'cau_hoi_id' => $cauHoiGoc->id],
    ['so_lan_sai' => 3, 'so_lan_dung' => 0, 'lan_cuoi_lam' => now()]
);

echo "   ✅ thong_ke_cau_hoi: so_lan_sai = {$thongKe->so_lan_sai} (>= 3 → trigger Adaptive AI)\n";

// Xóa biến thể cũ nếu có (để test sạch)
$bienTheCu = Question::where('cau_hoi_goc_id', $cauHoiGoc->id)->count();
if ($bienTheCu > 0) {
    Question::where('cau_hoi_goc_id', $cauHoiGoc->id)->forceDelete();
    echo "   🗑️  Đã xóa {$bienTheCu} biến thể cũ để test lại sạch.\n";
}

// ── BƯỚC 3: Dispatch Job đồng bộ ─────────────────────────
echo "\n🤖 [3] Gọi AdaptiveQuestionSynthesisJob (đồng bộ)...\n";
echo "   ⏳ Đang gọi Gemini API để sinh 3 câu biến thể, vui lòng chờ...\n\n";

$thoiGianBatDau = microtime(true);

AdaptiveQuestionSynthesisJob::dispatchSync($cauHoiGoc->id, $student->id);

$thoiGianXong = round(microtime(true) - $thoiGianBatDau, 2);
echo "⏱️  Thời gian xử lý: {$thoiGianXong}s\n\n";

// ── BƯỚC 4: Hiển thị kết quả ─────────────────────────────
$bienThe = Question::where('cau_hoi_goc_id', $cauHoiGoc->id)->with('luaChon')->get();

echo "╔══════════════════════════════════════════════════════╗\n";
echo "║  KẾT QUẢ: CÁC CÂU HỎI BIẾN THỂ DO AI SINH         ║\n";
echo "╚══════════════════════════════════════════════════════╝\n\n";

if ($bienThe->isEmpty()) {
    echo "❌ Không có biến thể được tạo. Kiểm tra: storage/logs/laravel.log\n\n";
    return;
}

echo "✅ Đã sinh thành công {$bienThe->count()} câu biến thể!\n\n";

foreach ($bienThe as $index => $bt) {
    echo "┌─ Biến thể #" . ($index + 1) . " (ID: #{$bt->id}) [nguon: {$bt->nguon->value}]\n";
    echo "│  📌 {$bt->noi_dung}\n";
    echo "│  📋 Lựa chọn:\n";
    foreach ($bt->luaChon->sortBy('thu_tu') as $lc) {
        $mark = $lc->la_dap_an ? ' ✅ (Đáp án đúng)' : '';
        echo "│      " . chr(65 + $lc->thu_tu) . ". {$lc->noi_dung}{$mark}\n";
    }
    echo "└" . str_repeat("─", 54) . "\n\n";
}

// Thống kê
echo "📊 Thống kê:\n";
echo "   - Câu gốc ID:           #{$cauHoiGoc->id}\n";
echo "   - Số biến thể đã sinh:  {$bienThe->count()}\n";
echo "   - do_ai_tao:            true\n";
echo "   - nguon:                ai_sinh\n";
echo "   - trang_thai:           da_duyet (dùng được ngay)\n";
echo "   - cau_hoi_goc_id:       #{$cauHoiGoc->id}\n";

echo "\n✅ BÀI TEST 2 HOÀN TẤT!\n\n";
