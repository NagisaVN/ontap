<?php

use App\Enums\AttemptStatus;
use App\Livewire\Teacher\Reports;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptAnswer;
use App\Models\Major;
use App\Models\Question;
use App\Models\Subject;
use App\Models\SubSubject;
use App\Models\User;
use App\Models\UserQuestionStat;
use App\Services\TeacherReportService;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

function makeTeacherReportFixture(): array
{
    app()[PermissionRegistrar::class]->forgetCachedPermissions();
    Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);

    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');
    $studentA = User::factory()->create(['name' => 'Nguyen Van An']);
    $studentB = User::factory()->create(['name' => 'Tran Thi Binh']);
    $major = Major::create(['ten' => 'Khoa học']);
    $subject = Subject::create([
        'nganh_id' => $major->id,
        'ten' => 'Vật lý',
        'ma_mon' => 'PHY101',
    ]);
    $chapter = SubSubject::create([
        'mon_hoc_id' => $subject->id,
        'ten' => 'Điện từ học',
        'thu_tu' => 1,
    ]);
    $question = Question::create([
        'chuong_id' => $chapter->id,
        'noi_dung' => 'Câu hỏi có tỷ lệ sai cao?',
        'do_kho' => 'kho',
        'trang_thai' => 'da_duyet',
    ]);
    $exam = Exam::create([
        'nguoi_dung_id' => $teacher->id,
        'mon_hoc_id' => $subject->id,
        'ten_bai_thi' => 'Đề Điện từ',
        'so_cau_hoi' => 1,
        'thoi_gian_phut' => 30,
    ]);

    $attemptA = ExamAttempt::create([
        'nguoi_dung_id' => $studentA->id,
        'bai_thi_id' => $exam->id,
        'diem_so' => 9,
        'so_cau_dung' => 1,
        'trang_thai' => AttemptStatus::HoanThanh,
        'ket_thuc_luc' => now(),
    ]);
    $attemptB = ExamAttempt::create([
        'nguoi_dung_id' => $studentB->id,
        'bai_thi_id' => $exam->id,
        'diem_so' => 5,
        'so_cau_dung' => 0,
        'trang_thai' => AttemptStatus::HoanThanh,
        'ket_thuc_luc' => now(),
    ]);
    ExamAttemptAnswer::create([
        'luot_thi_id' => $attemptA->id,
        'cau_hoi_id' => $question->id,
        'dung_sai' => true,
    ]);
    ExamAttemptAnswer::create([
        'luot_thi_id' => $attemptB->id,
        'cau_hoi_id' => $question->id,
        'dung_sai' => false,
    ]);
    UserQuestionStat::create([
        'nguoi_dung_id' => $studentA->id,
        'cau_hoi_id' => $question->id,
        'so_lan_dung' => 1,
        'so_lan_sai' => 2,
    ]);
    UserQuestionStat::create([
        'nguoi_dung_id' => $studentB->id,
        'cau_hoi_id' => $question->id,
        'so_lan_dung' => 0,
        'so_lan_sai' => 3,
    ]);

    return compact('teacher', 'studentA', 'studentB', 'subject', 'question');
}

it('builds a teacher-scoped report from exam attempts and question statistics', function () {
    ['teacher' => $teacher, 'subject' => $subject, 'question' => $question] = makeTeacherReportFixture();

    $report = app(TeacherReportService::class)->build($teacher, ['subject_id' => $subject->id]);

    expect($report['summary'])
        ->students->toBe(2)
        ->attempts->toBe(2)
        ->averageScore->toBe(70.0)
        ->passRate->toBe(100.0)
        ->and($report['roster'][0]['name'])->toBe('Nguyen Van An')
        ->and($report['scoreDistribution'][1]['count'])->toBe(1)
        ->and($report['scoreDistribution'][3]['count'])->toBe(1)
        ->and($report['difficultyAnalysis'][0]['questionId'])->toBe($question->id)
        ->and($report['difficultyAnalysis'][0]['failRate'])->toBe(50.0)
        ->and($report['difficultyAnalysis'][0]['practiceAttempts'])->toBe(6)
        ->and($report['difficultyAnalysis'][0]['practiceFailRate'])->toBe(83.3);
});

it('renders teacher reports with real values and export links', function () {
    ['teacher' => $teacher, 'subject' => $subject] = makeTeacherReportFixture();
    $this->actingAs($teacher);

    Livewire::test(Reports::class)
        ->set('subjectId', (string) $subject->id)
        ->assertSee('Nguyen Van An')
        ->assertSee('Tran Thi Binh')
        ->assertSee('Câu hỏi có tỷ lệ sai cao?')
        ->assertSee('Xuất PDF')
        ->assertSee('Xuất Excel');
});

it('excludes attempts from exams created by another teacher', function () {
    ['teacher' => $teacher, 'subject' => $subject, 'question' => $question] = makeTeacherReportFixture();
    $otherTeacher = User::factory()->create();
    $otherTeacher->assignRole('teacher');
    $otherStudent = User::factory()->create(['name' => 'Khong Thuoc Cohort']);
    $otherExam = Exam::create([
        'nguoi_dung_id' => $otherTeacher->id,
        'mon_hoc_id' => $subject->id,
        'ten_bai_thi' => 'Đề của giáo viên khác',
        'so_cau_hoi' => 1,
        'thoi_gian_phut' => 30,
    ]);
    $otherAttempt = ExamAttempt::create([
        'nguoi_dung_id' => $otherStudent->id,
        'bai_thi_id' => $otherExam->id,
        'diem_so' => 10,
        'so_cau_dung' => 1,
        'trang_thai' => AttemptStatus::HoanThanh,
        'ket_thuc_luc' => now(),
    ]);
    ExamAttemptAnswer::create([
        'luot_thi_id' => $otherAttempt->id,
        'cau_hoi_id' => $question->id,
        'dung_sai' => true,
    ]);

    $report = app(TeacherReportService::class)->build($teacher, ['subject_id' => $subject->id]);

    expect($report['summary']['students'])->toBe(2)
        ->and(collect($report['roster'])->pluck('name'))->not->toContain('Khong Thuoc Cohort')
        ->and($report['difficultyAnalysis'][0]['responses'])->toBe(2);
});

it('downloads the current teacher report as PDF and Excel', function () {
    ['teacher' => $teacher, 'subject' => $subject] = makeTeacherReportFixture();
    $this->actingAs($teacher);

    $this->get(route('teacher.reports.export', ['format' => 'pdf', 'subject_id' => $subject->id]))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    $this->get(route('teacher.reports.export', ['format' => 'excel', 'subject_id' => $subject->id]))
        ->assertOk()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});
