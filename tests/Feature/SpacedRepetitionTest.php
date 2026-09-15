<?php

use App\Livewire\Student\SpacedRepetition;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Major;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\SpacedRepetitionSchedule;
use App\Models\Subject;
use App\Models\SubSubject;
use App\Models\User;
use App\Models\UserQuestionStat;
use App\Notifications\ReviewReminderNotification;
use App\Services\ExamGradingService;
use App\Services\ProgressTrackingService;
use App\Services\SpacedRepetitionService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

function makeReviewQuestion(): Question
{
    $major = Major::create(['ten' => 'Test major']);
    $subject = Subject::create([
        'nganh_id' => $major->id,
        'ten' => 'Test subject',
        'ma_mon' => 'SR'.fake()->unique()->numerify('###'),
    ]);
    $chapter = SubSubject::create([
        'mon_hoc_id' => $subject->id,
        'ten' => 'Test chapter',
        'thu_tu' => 1,
    ]);
    $question = Question::create([
        'chuong_id' => $chapter->id,
        'noi_dung' => 'What is 2 + 2?',
        'do_kho' => 'de',
        'trang_thai' => 'da_duyet',
    ]);
    QuestionOption::create([
        'cau_hoi_id' => $question->id,
        'noi_dung' => '4',
        'la_dap_an' => true,
        'thu_tu' => 1,
    ]);

    return $question;
}

it('applies the SM-2 interval and ease-factor transitions', function () {
    Carbon::setTestNow('2026-09-15 08:00:00');

    $user = User::factory()->create();
    $question = makeReviewQuestion();
    $service = app(SpacedRepetitionService::class);
    $schedule = $service->scheduleWrongAnswer($user->id, $question->id);

    expect($schedule)
        ->repetitions->toBe(0)
        ->interval_days->toBe(0)
        ->ease_factor->toBe(2.5)
        ->and($schedule->next_review_at->equalTo(now()))->toBeTrue();

    $schedule = $service->review($schedule, 'hard');
    expect($schedule)
        ->repetitions->toBe(0)
        ->interval_days->toBe(1)
        ->ease_factor->toBe(1.96)
        ->and($schedule->next_review_at->equalTo(now()->addDay()))->toBeTrue();

    $schedule = $service->review($schedule, 'medium');
    expect($schedule)
        ->repetitions->toBe(1)
        ->interval_days->toBe(1)
        ->ease_factor->toBe(1.82);

    $schedule = $service->review($schedule, 'medium');
    expect($schedule)
        ->repetitions->toBe(2)
        ->interval_days->toBe(6)
        ->ease_factor->toBe(1.68);

    $schedule = $service->review($schedule, 'easy');
    expect($schedule)
        ->repetitions->toBe(3)
        ->interval_days->toBe(11)
        ->ease_factor->toBe(1.78);

    Carbon::setTestNow();
});

it('uses ProgressTrackingService to turn historical wrong answers into due cards', function () {
    $user = User::factory()->create();
    $question = makeReviewQuestion();
    UserQuestionStat::create([
        'nguoi_dung_id' => $user->id,
        'cau_hoi_id' => $question->id,
        'so_lan_sai' => 2,
        'so_lan_dung' => 0,
        'lan_cuoi_lam' => now()->subDay(),
    ]);

    $queue = app(ProgressTrackingService::class)->layHangDoiOnTap($user);

    expect($queue)->toHaveCount(1)
        ->and($queue->first()['cau_hoi']->is($question))->toBeTrue()
        ->and($queue->first()['schedule'])->toBeInstanceOf(SpacedRepetitionSchedule::class);

    $this->assertDatabaseHas('lich_on_tap', [
        'nguoi_dung_id' => $user->id,
        'cau_hoi_id' => $question->id,
        'repetitions' => 0,
    ]);
});

it('creates a due schedule when grading a wrong exam answer', function () {
    $student = User::factory()->create();
    $question = makeReviewQuestion()->load('chuong.monHoc');
    $exam = Exam::create([
        'nguoi_dung_id' => User::factory()->create()->id,
        'mon_hoc_id' => $question->chuong->mon_hoc_id,
        'ten_bai_thi' => 'Review source exam',
        'so_cau_hoi' => 1,
        'thoi_gian_phut' => 10,
    ]);
    $exam->cauHoi()->attach($question->id, ['thu_tu' => 1]);
    $attempt = ExamAttempt::create([
        'nguoi_dung_id' => $student->id,
        'bai_thi_id' => $exam->id,
        'bat_dau_luc' => now()->subMinute(),
    ]);

    app(ExamGradingService::class)->cham($attempt, [[
        'cau_hoi_id' => $question->id,
        'lua_chon_id' => null,
    ]]);

    $this->assertDatabaseHas('lich_on_tap', [
        'nguoi_dung_id' => $student->id,
        'cau_hoi_id' => $question->id,
        'repetitions' => 0,
        'interval_days' => 0,
    ]);
});

it('renders and rates the real due-card queue in the Livewire review screen', function () {
    $user = User::factory()->create();
    $question = makeReviewQuestion();
    $schedule = app(SpacedRepetitionService::class)->scheduleWrongAnswer($user->id, $question->id);

    $this->actingAs($user);

    Livewire::test(SpacedRepetition::class)
        ->assertSet('currentScheduleId', $schedule->id)
        ->assertSet('sessionTotal', 1)
        ->assertSee('Ôn tập lặp lại')
        ->assertSee('1 / 1 thẻ')
        ->assertSee('Câu hỏi')
        ->assertSee('Lật thẻ để tự đánh giá mức độ ghi nhớ')
        ->assertSee('What is 2 + 2?')
        ->call('rateCard', 'easy')
        ->assertSet('sessionCount', 1)
        ->assertSet('ratingCounts.easy', 1)
        ->assertSee('Hoàn thành phiên ôn tập!')
        ->assertSet('currentScheduleId', null);

    expect($schedule->fresh())
        ->repetitions->toBe(1)
        ->interval_days->toBe(1)
        ->and($schedule->fresh()->next_review_at->isFuture())->toBeTrue();
});

it('keeps every due card in a 128-card review session', function () {
    $user = User::factory()->create();
    $templateQuestion = makeReviewQuestion();
    $now = now();

    DB::table('cau_hoi')->insert(
        collect(range(1, 128))->map(fn (int $number) => [
            'chuong_id' => $templateQuestion->chuong_id,
            'noi_dung' => "Queue card {$number}",
            'do_kho' => 'de',
            'created_at' => $now,
            'updated_at' => $now,
        ])->all(),
    );

    $questionIds = Question::query()
        ->where('chuong_id', $templateQuestion->chuong_id)
        ->where('noi_dung', 'like', 'Queue card %')
        ->pluck('id');

    SpacedRepetitionSchedule::insert(
        $questionIds->map(fn (int $questionId) => [
            'nguoi_dung_id' => $user->id,
            'cau_hoi_id' => $questionId,
            'interval_days' => 0,
            'repetitions' => 0,
            'ease_factor' => 2.5,
            'next_review_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all(),
    );

    $this->actingAs($user);

    Livewire::test(SpacedRepetition::class)
        ->assertSet('sessionTotal', 128)
        ->assertSee('1 / 128 thẻ');
});

it('sends one daily email reminder for cards due today', function () {
    Carbon::setTestNow('2026-09-15 08:00:00');
    Notification::fake();

    $user = User::factory()->create();
    $question = makeReviewQuestion();
    app(SpacedRepetitionService::class)->scheduleWrongAnswer($user->id, $question->id);

    Artisan::call('reviews:send-reminders');

    Notification::assertSentTo($user, ReviewReminderNotification::class);
    expect(SpacedRepetitionSchedule::firstOrFail()->reminder_sent_at)->not->toBeNull();

    Artisan::call('reviews:send-reminders');
    Notification::assertSentToTimes($user, ReviewReminderNotification::class, 1);

    Carbon::setTestNow();
});
