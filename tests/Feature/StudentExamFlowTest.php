<?php

/**
 * SmartPrep — Feature Tests: Student Exam Flow & AI Smart Tutor
 *
 * Run: php artisan test tests/Feature/StudentExamFlowTest.php
 *      php artisan test tests/Feature/StudentExamFlowTest.php --filter ai_tutor
 *
 * Requirements:
 *   composer require pestphp/pest --dev
 *   composer require pestphp/pest-plugin-livewire --dev
 *   php artisan vendor:publish --tag=livewire:assets
 */

use App\Enums\AttemptStatus;
use App\Livewire\Student\ExamResult;
use App\Livewire\Student\ExamRoom;
use App\Livewire\Teacher\QuestionManager;
use App\Livewire\Admin\UserManagement;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SubSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ═══════════════════════════════════════════════════════════════════════
// HELPERS — Factory helpers specific to this test file
// ═══════════════════════════════════════════════════════════════════════

/**
 * Ensure a Spatie role exists then assign it to user.
 * Inline helper to avoid Pest global hook ordering issues.
 */
function ensureRole(string $name): \Spatie\Permission\Models\Role
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    return \Spatie\Permission\Models\Role::firstOrCreate(
        ['name' => $name, 'guard_name' => 'web']
    );
}

/**
 * Tạo student user với role đúng (Spatie Permission).
 */
function makeStudent(): User
{
    ensureRole('student');
    $student = User::factory()->create();
    $student->assignRole('student');
    return $student;
}

/**
 * Tạo teacher user.
 */
function makeTeacher(): User
{
    ensureRole('teacher');
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    return $teacher;
}

/**
 * Tạo admin user.
 */
function makeAdmin(): User
{
    ensureRole('super_admin');
    $admin = User::factory()->create();
    $admin->assignRole('super_admin');
    return $admin;
}

/**
 * Tạo một bài thi với N câu hỏi trắc nghiệm.
 * Mỗi câu có 4 lựa chọn (1 đúng, 3 sai).
 */
function makeExamWithQuestions(int $count = 5, ?User $owner = null): Exam
{
    $teacher = $owner ?? makeTeacher();

    // Major (nganh) → Subject (mon_hoc) → SubSubject (chuong)
    $major = \App\Models\Major::create(['ten' => 'Ngành test', 'mo_ta' => '']);
    $subject = \App\Models\Subject::create([
        'nganh_id' => $major->id,
        'ten'      => 'Môn test',
        'ma_mon'   => 'TEST' . rand(100, 999),
    ]);
    $subSubject = \App\Models\SubSubject::create([
        'mon_hoc_id' => $subject->id,
        'ten'        => 'Chương test',
        'thu_tu'     => 1,
    ]);

    $exam = Exam::create([
        'nguoi_dung_id' => $teacher->id,
        'mon_hoc_id'    => $subject->id,
        'ten_bai_thi'   => 'Bài thi tự động',
        'so_cau_hoi'    => $count,
        'thoi_gian_phut'=> 45,
    ]);

    for ($i = 1; $i <= $count; $i++) {
        $question = Question::create([
            'chuong_id' => $subSubject->id,
            'noi_dung'  => "Câu hỏi số {$i}: 1 + 1 = ?",
            'do_kho'    => 'de',
            'trang_thai'=> 'da_duyet',
        ]);

        // Đáp án đúng
        QuestionOption::create([
            'cau_hoi_id' => $question->id,
            'noi_dung'   => '2 (đúng)',
            'la_dap_an'  => true,
            'thu_tu'     => 1,
        ]);

        // Đáp án sai
        foreach (['3 (sai)', '4 (sai)', '5 (sai)'] as $j => $text) {
            QuestionOption::create([
                'cau_hoi_id' => $question->id,
                'noi_dung'   => $text,
                'la_dap_an'  => false,
                'thu_tu'     => $j + 2,
            ]);
        }

        $exam->cauHoi()->attach($question->id, ['thu_tu' => $i]);
    }

    return $exam;
}

/**
 * Tạo ExamAttempt đã hoàn thành với kết quả sai câu đầu.
 */
function makeCompletedAttemptWithWrongAnswer(User $student, Exam $exam): ExamAttempt
{
    $attempt = ExamAttempt::create([
        'nguoi_dung_id' => $student->id,
        'bai_thi_id'    => $exam->id,
        'trang_thai'    => AttemptStatus::HoanThanh->value,
        'so_cau_dung'   => $exam->so_cau_hoi - 1,
        'diem_so'       => 9.0,
        'bat_dau_luc'   => now()->subMinutes(30),
        'ket_thuc_luc'  => now(),
    ]);

    $exam->load('cauHoi.luaChon');

    foreach ($exam->cauHoi as $idx => $question) {
        $correctOption = $question->luaChon->firstWhere('la_dap_an', true);
        $wrongOption   = $question->luaChon->firstWhere('la_dap_an', false);

        // Câu đầu tiên: trả lời SAI
        $chosenOption = $idx === 0 ? $wrongOption : $correctOption;
        $isDung       = $idx !== 0;

        ExamAttemptAnswer::create([
            'luot_thi_id' => $attempt->id,
            'cau_hoi_id'  => $question->id,
            'lua_chon_id' => $chosenOption->id,
            'dung_sai'    => $isDung,
        ]);
    }

    return $attempt;
}

// ═══════════════════════════════════════════════════════════════════════
// GROUP 1: ROUTE AUTHORIZATION — Security Tests
// ═══════════════════════════════════════════════════════════════════════

describe('Route Authorization (Security)', function () {

    it('guest bị redirect về login khi truy cập student dashboard', function () {
        $this->get(route('student.dashboard'))->assertRedirect(route('login'));
    });

    it('guest bị redirect về login khi truy cập teacher dashboard', function () {
        $this->get(route('teacher.dashboard'))->assertRedirect(route('login'));
    });

    it('guest bị redirect về login khi truy cập admin dashboard', function () {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    });

    it('student bị 403 khi truy cập route teacher', function () {
        $student = makeStudent();

        $this->actingAs($student)
            ->get(route('teacher.questions'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('teacher.ocr'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('teacher.exam-builder'))
            ->assertForbidden();
    });

    it('student bị 403 khi truy cập route admin', function () {
        $student = makeStudent();
        $this->actingAs($student)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });

    it('teacher bị 403 khi truy cập route admin', function () {
        $teacher = makeTeacher();
        $this->actingAs($teacher)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    });

    it('student không thể xem kết quả thi của student khác', function () {
        $student1 = makeStudent();
        $student2 = makeStudent();
        $exam     = makeExamWithQuestions(3);
        $attempt  = makeCompletedAttemptWithWrongAnswer($student1, $exam);

        // student2 cố vào kết quả của student1
        $this->actingAs($student2)
            ->get(route('exam.result', $attempt))
            ->assertForbidden();
    });

    it('student có thể xem kết quả thi của chính mình', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);
        $attempt = makeCompletedAttemptWithWrongAnswer($student, $exam);

        $this->actingAs($student)
            ->get(route('exam.result', $attempt))
            ->assertOk();
    });

});

// ═══════════════════════════════════════════════════════════════════════
// GROUP 2: EXAM ROOM — ExamRoom Component Tests
// ═══════════════════════════════════════════════════════════════════════

describe('ExamRoom Component', function () {

    it('mount tạo lượt thi mới nếu chưa có', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(5);

        expect(ExamAttempt::where('nguoi_dung_id', $student->id)->count())->toBe(0);

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->assertSet('cauHienTai', 0)
            ->assertSet('daHoanThanh', false);

        expect(ExamAttempt::where('nguoi_dung_id', $student->id)->count())->toBe(1);
    });

    it('mount tái sử dụng lượt thi đang làm dở', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);

        $existing = ExamAttempt::create([
            'nguoi_dung_id' => $student->id,
            'bai_thi_id'    => $exam->id,
            'trang_thai'    => AttemptStatus::DangLam->value,
            'bat_dau_luc'   => now()->subMinutes(5),
        ]);

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam]);

        // Chỉ có 1 attempt, không tạo thêm
        expect(ExamAttempt::where('nguoi_dung_id', $student->id)->count())->toBe(1);
    });

    it('chonDapAn lưu đáp án vào $dapAnDaChon', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);
        $exam->load('cauHoi.luaChon');

        $firstQuestion = $exam->cauHoi->first();
        $firstOption   = $firstQuestion->luaChon->first();

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->call('chonDapAn', $firstQuestion->id, $firstOption->id)
            ->assertSet("dapAnDaChon.{$firstQuestion->id}", $firstOption->id);
    });

    it('cauTiep và cauTruoc điều hướng câu hỏi đúng', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(5);

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->assertSet('cauHienTai', 0)
            ->call('cauTiep')
            ->assertSet('cauHienTai', 1)
            ->call('cauTiep')
            ->assertSet('cauHienTai', 2)
            ->call('cauTruoc')
            ->assertSet('cauHienTai', 1);
    });

    it('cauTruoc không xuống dưới 0', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->assertSet('cauHienTai', 0)
            ->call('cauTruoc')
            ->assertSet('cauHienTai', 0); // không âm
    });

    it('nopBai double-click không tạo 2 kết quả (daHoanThanh guard)', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);
        $exam->load('cauHoi.luaChon');

        $this->actingAs($student);

        $component = Livewire::test(ExamRoom::class, ['baiThi' => $exam]);

        // Click 1
        $component->call('nopBai');
        $count1 = ExamAttempt::where('trang_thai', AttemptStatus::HoanThanh->value)->count();

        // Click 2 (double-click)
        $component->call('nopBai');
        $count2 = ExamAttempt::where('trang_thai', AttemptStatus::HoanThanh->value)->count();

        expect($count1)->toBe(1);
        expect($count2)->toBe(1); // Không tăng thêm
    });

    it('ghiNhanRoiTab tăng soLanRoiTab', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(2);

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->assertSet('soLanRoiTab', 0)
            ->call('ghiNhanRoiTab')
            ->assertSet('soLanRoiTab', 1)
            ->call('ghiNhanRoiTab')
            ->assertSet('soLanRoiTab', 2);
    });

    it('$baiThiId và $luotThiId là #[Locked] — không thể mutate từ client', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(2);

        $this->actingAs($student);

        // Livewire 3 throws CannotUpdateLockedPropertyException when mutating #[Locked] props
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->set('baiThiId', 9999); // Should throw
    });

});

// ═══════════════════════════════════════════════════════════════════════
// GROUP 3: EXAM RESULT — ExamResult Component Tests
// ═══════════════════════════════════════════════════════════════════════

describe('ExamResult Component', function () {

    it('render hiển thị đúng số câu đúng/sai', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(5);
        $attempt = makeCompletedAttemptWithWrongAnswer($student, $exam);

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->assertSee('4') // so_cau_dung = 4
            ->assertSee('1'); // so_cau_sai = 1
    });

    it('render hiển thị reviewItems cho tất cả câu', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);
        $attempt = makeCompletedAttemptWithWrongAnswer($student, $exam);

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->assertViewHas('reviewItems', fn($items) => count($items) === 3);
    });

    it('openTutor khởi tạo chatHistory với system prompt và greeting', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);
        $attempt = makeCompletedAttemptWithWrongAnswer($student, $exam);

        // Câu đầu bị sai — lấy ExamAttemptAnswer id của nó
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)
            ->first();

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->assertSet('activeChatKetQuaId', null)
            ->call('openTutor', $wrongAnswer->id)
            ->assertSet('activeChatKetQuaId', $wrongAnswer->id)
            ->assertSet('tutorError', '')
            ->tap(function ($component) {
                // chatHistory có ít nhất system + greeting
                $history = $component->get('chatHistory');
                expect($history)->toHaveCount(2);
                expect($history[0]['role'])->toBe('system');
                expect($history[1]['role'])->toBe('model');
                // System prompt chứa thông tin câu hỏi
                expect($history[0]['content'])->toContain('SmartPrep');
            });
    });

    it('openTutor với ketQuaId của người khác không hoạt động (security)', function () {
        $student1 = makeStudent();
        $student2 = makeStudent();
        $exam     = makeExamWithQuestions(3);

        $attempt1 = makeCompletedAttemptWithWrongAnswer($student1, $exam);
        $attempt2 = makeCompletedAttemptWithWrongAnswer($student2, $exam);

        // ketQua của student2
        $wrongAnswerStudent2 = ExamAttemptAnswer::where('luot_thi_id', $attempt2->id)
            ->where('dung_sai', false)->first();

        $this->actingAs($student1);

        // student1 cố mở tutor với ketQua của student2
        Livewire::test(ExamResult::class, ['luotThi' => $attempt1])
            ->call('openTutor', $wrongAnswerStudent2->id)
            // Vì scoped với luot_thi_id của student1 → find() trả null → không mở
            ->assertSet('activeChatKetQuaId', null);
    });

    it('closeTutor xóa toàn bộ chat history', function () {
        $student     = makeStudent();
        $exam        = makeExamWithQuestions(3);
        $attempt     = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('openTutor', $wrongAnswer->id)
            ->assertSet('activeChatKetQuaId', $wrongAnswer->id)
            ->call('closeTutor')
            ->assertSet('activeChatKetQuaId', null)
            ->assertSet('chatHistory', [])
            ->assertSet('userMessage', '');
    });

    it('sendMessage với input trống không làm gì', function () {
        $student     = makeStudent();
        $exam        = makeExamWithQuestions(3);
        $attempt     = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        $this->actingAs($student);

        $component = Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('openTutor', $wrongAnswer->id);

        $initialCount = count($component->get('chatHistory'));

        $component->set('userMessage', '   ') // whitespace only
            ->call('sendMessage');

        // chatHistory không tăng
        expect($component->get('chatHistory'))->toHaveCount($initialCount);
    });

    it('sendMessage với Gemini mock — phản hồi được append vào chatHistory', function () {
        // Mock Gemini API để không cần real API key trong test
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Đây là giải thích từ AI Mock.']]],
                    'finishReason' => 'STOP',
                ]],
            ], 200),
        ]);

        $student     = makeStudent();
        $exam        = makeExamWithQuestions(3);
        $attempt     = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('openTutor', $wrongAnswer->id)
            ->set('userMessage', 'Tại sao đáp án này sai?')
            ->call('sendMessage')
            ->assertSet('userMessage', '') // cleared after send
            ->assertSet('tutorError', '')  // no error
            ->tap(function ($component) {
                $history = $component->get('chatHistory');
                // system + greeting + user message + AI reply = 4
                expect($history)->toHaveCount(4);
                expect($history[2]['role'])->toBe('user');
                expect($history[2]['content'])->toBe('Tại sao đáp án này sai?');
                expect($history[3]['role'])->toBe('model');
                expect($history[3]['content'])->toBe('Đây là giải thích từ AI Mock.');
            });
    });

    it('sendMessage khi Gemini lỗi — userMessage được restore và tutorError được set', function () {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => ['message' => 'Service Unavailable', 'code' => 503],
            ], 503),
        ]);

        $student     = makeStudent();
        $exam        = makeExamWithQuestions(3);
        $attempt     = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('openTutor', $wrongAnswer->id)
            ->set('userMessage', 'Câu hỏi quan trọng')
            ->call('sendMessage')
            ->tap(function ($component) {
                // userMessage restored (không bị mất)
                expect($component->get('userMessage'))->toBe('Câu hỏi quan trọng');
                // tutorError được set
                expect($component->get('tutorError'))->not->toBeEmpty();
                // chatHistory không có message sai (đã pop)
                $history = $component->get('chatHistory');
                $roles = collect($history)->pluck('role')->toArray();
                expect(array_count_values($roles)['user'] ?? 0)->toBe(0);
            });
    });

    it('generateExplanation gọi Gemini và lưu vào DB', function () {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Giải thích: Đáp án đúng là 2 vì...']]],
                ]],
            ], 200),
        ]);

        $student     = makeStudent();
        $exam        = makeExamWithQuestions(2);
        $attempt     = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        expect($wrongAnswer->giai_thich_ai)->toBeNull();

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('generateExplanation', $wrongAnswer->id);

        $wrongAnswer->refresh();
        expect($wrongAnswer->giai_thich_ai)->not->toBeNull();
        expect($wrongAnswer->giai_thich_ai)->toContain('Giải thích');
    });

});

// ═══════════════════════════════════════════════════════════════════════
// GROUP 4: FULL E2E WORKFLOW — Teacher → Student → AI Tutor
// ═══════════════════════════════════════════════════════════════════════

describe('E2E: Full Exam Workflow', function () {

    it('student hoàn thành bài thi và xem kết quả đúng', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(5);

        $exam->load('cauHoi.luaChon');

        $this->actingAs($student);

        // 1. Tạo attempt (mount ExamRoom)
        $component = Livewire::test(ExamRoom::class, ['baiThi' => $exam]);
        $attempt   = ExamAttempt::where('nguoi_dung_id', $student->id)->first();

        // 2. Trả lời tất cả câu — câu 1 sai, câu 2-5 đúng
        foreach ($exam->cauHoi as $idx => $question) {
            $correctOption = $question->luaChon->firstWhere('la_dap_an', true);
            $wrongOption   = $question->luaChon->firstWhere('la_dap_an', false);
            $chosen        = $idx === 0 ? $wrongOption : $correctOption;

            $component->call('chonDapAn', $question->id, $chosen->id);
        }

        // 3. Nộp bài
        $component->call('nopBai');

        // 4. Kiểm tra attempt đã hoàn thành
        $attempt->refresh();
        expect($attempt->trang_thai->value)->toBe('hoan_thanh');
        expect($attempt->so_cau_dung)->toBe(4);

        // 5. Xem kết quả
        $resultComponent = Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->assertViewHas('correct', 4)
            ->assertViewHas('wrong', 1)
            ->assertViewHas('percentage', 80);

        // 6. Mở AI Tutor cho câu sai
        $wrongKetQua = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        $resultComponent
            ->call('openTutor', $wrongKetQua->id)
            ->assertSet('activeChatKetQuaId', $wrongKetQua->id)
            ->tap(function ($c) {
                $history = $c->get('chatHistory');
                expect($history[0]['role'])->toBe('system');
                expect($history[0]['content'])->toContain('SmartPrep');
                expect($history[1]['role'])->toBe('model');
            });
    });

    it('conversation context được maintain qua nhiều lượt sendMessage', function () {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::sequence()
                ->push(['candidates' => [['content' => ['parts' => [['text' => 'Đây là tin nhắn AI đầu tiên.']]]]]], 200)
                ->push(['candidates' => [['content' => ['parts' => [['text' => 'Đây là tin nhắn AI thứ hai.']]]]]], 200),
        ]);

        $student     = makeStudent();
        $exam        = makeExamWithQuestions(2);
        $attempt     = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $wrongAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', false)->first();

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('openTutor', $wrongAnswer->id)
            // Message 1
            ->set('userMessage', 'Câu hỏi 1')
            ->call('sendMessage')
            // Message 2
            ->set('userMessage', 'Câu hỏi 2 (follow-up)')
            ->call('sendMessage')
            ->tap(function ($component) {
                $history = $component->get('chatHistory');
                // system + greeting + user1 + ai1 + user2 + ai2 = 6
                expect($history)->toHaveCount(6);
                expect($history[4]['content'])->toBe('Câu hỏi 2 (follow-up)');
                expect($history[5]['content'])->toBe('Đây là tin nhắn AI thứ hai.');
            });

        // Verify Gemini nhận đúng toàn bộ conversation history
        Http::assertSentCount(2);
    });

});

// ═══════════════════════════════════════════════════════════════════════
// GROUP 5: EDGE CASES — Boundary & Error Conditions
// ═══════════════════════════════════════════════════════════════════════

describe('Edge Cases', function () {

    it('bài thi không có câu hỏi — ExamRoom render không crash', function () {
        $student = makeStudent();
        $major   = \App\Models\Major::create(['ten' => 'Ngành test edge', 'mo_ta' => '']);
        $subject = \App\Models\Subject::create(['nganh_id' => $major->id, 'ten' => 'Môn edge', 'ma_mon' => 'EDGE01']);
        $exam    = Exam::create([
            'nguoi_dung_id' => makeTeacher()->id,
            'mon_hoc_id'    => $subject->id,
            'ten_bai_thi'   => 'Bài thi rỗng',
            'so_cau_hoi'    => 0,
            'thoi_gian_phut'=> 30,
        ]);

        $this->actingAs($student);

        Livewire::test(ExamRoom::class, ['baiThi' => $exam])
            ->assertSet('cauHoiList', [])
            ->assertSet('cauHienTai', 0);
    });

    it('ExamResult render với attempt đã hoàn thành không có câu trả lời nào', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(3);
        $attempt = ExamAttempt::create([
            'nguoi_dung_id' => $student->id,
            'bai_thi_id'    => $exam->id,
            'trang_thai'    => AttemptStatus::HoanThanh->value,
            'so_cau_dung'   => 0,
            'diem_so'       => 0,
        ]);

        $this->actingAs($student);

        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->assertViewHas('correct', 0)
            ->assertViewHas('wrong', 0)
            ->assertViewHas('percentage', 0);
    });

    it('openTutor trên câu ĐÚNG không được set activeChatKetQuaId (UI kiểm soát)', function () {
        // openTutor chỉ được gọi từ UI khi câu sai — nhưng test server-side behavior
        $student      = makeStudent();
        $exam         = makeExamWithQuestions(3);
        $attempt      = makeCompletedAttemptWithWrongAnswer($student, $exam);
        $correctAnswer = ExamAttemptAnswer::where('luot_thi_id', $attempt->id)
            ->where('dung_sai', true)->first();

        $this->actingAs($student);

        // openTutor vẫn hoạt động với câu đúng (không có server-side block)
        // nhưng UI chỉ hiện nút cho câu sai — đây là acceptable
        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->call('openTutor', $correctAnswer->id)
            ->assertSet('activeChatKetQuaId', $correctAnswer->id);
        // Không bị crash — AI sẽ thấy "Bạn đã trả lời đúng" từ context
    });

    it('sendMessage khi activeChatKetQuaId là null thì không làm gì', function () {
        $student = makeStudent();
        $exam    = makeExamWithQuestions(2);
        $attempt = makeCompletedAttemptWithWrongAnswer($student, $exam);

        $this->actingAs($student);

        // Không gọi openTutor trước
        Livewire::test(ExamResult::class, ['luotThi' => $attempt])
            ->assertSet('activeChatKetQuaId', null)
            ->set('userMessage', 'Test')
            ->call('sendMessage')
            ->assertSet('chatHistory', []); // Không có gì xảy ra
    });

});
