<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin;
use App\Livewire\Student;
use App\Livewire\Teacher;

// ── Root redirect: đã login → dashboard theo role, chưa login → login ──
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return match (true) {
            $user->hasRole('super_admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('teacher')     => redirect()->route('teacher.dashboard'),
            default                       => redirect()->route('student.dashboard'),
        };
    }
    return redirect()->route('login');
});

// ── Profile ─────────────────────────────────────────────────
Route::get('/profile', \App\Livewire\Profile::class)
    ->middleware(['auth'])
    ->name('profile');

// ── Authenticated routes ─────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Smart dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('super_admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('teacher')     => redirect()->route('teacher.dashboard'),
            default                       => redirect()->route('student.dashboard'),
        };
    })->name('dashboard');

    // ── Student ─────────────────────────────────────────────
    Route::prefix('student')
        ->middleware('role:student|super_admin')
        ->group(function () {
            Route::get('/',              Student\Dashboard::class)       ->name('student.dashboard');
            Route::get('/history',       Student\History::class)          ->name('student.history');
            Route::get('/on-tap',        Student\SpacedRepetition::class) ->name('student.on-tap');
            Route::get('/leaderboard',   Student\Leaderboard::class)      ->name('student.leaderboard');

            // Exam flow
            Route::get('/thi',                    Student\CreateExam::class)  ->name('student.thi');
            Route::get('/thi/{baiThi}',           Student\ExamRoom::class)    ->name('exam.room');
            Route::get('/ket-qua/{luotThi}',      Student\ExamResult::class)  ->name('exam.result');
        });

    // ── Teacher ─────────────────────────────────────────────
    Route::prefix('giao-vien')
        ->middleware('role:teacher|super_admin')
        ->group(function () {
            Route::get('/',             Teacher\Dashboard::class)       ->name('teacher.dashboard');
            Route::get('/cau-hoi',             Teacher\QuestionManager::class) ->name('teacher.questions');
            Route::get('/cau-hoi/{id}/sua',    Teacher\QuestionEditor::class)  ->name('teacher.edit-question');
            Route::get('/tao-cau-hoi',         Teacher\QuestionCreator::class) ->name('teacher.create-question');
            Route::get('/de-thi',       Teacher\ExamBuilder::class)     ->name('teacher.exam-builder');
            Route::get('/bao-cao',      Teacher\Reports::class)          ->name('teacher.reports');

            // Keep legacy routes pointing to existing classes
            Route::get('/cho-duyet',   Teacher\PendingReview::class)    ->name('teacher.pending');
            Route::get('/ocr-upload',  Teacher\OcrUpload::class)         ->name('teacher.ocr');
        });

    // ── Admin ───────────────────────────────────────────────
    Route::prefix('quan-tri')
        ->middleware('role:super_admin')
        ->group(function () {
            Route::get('/',           Admin\Dashboard::class)       ->name('admin.dashboard');
            Route::get('/cau-truc',   Admin\TaxonomyManager::class) ->name('admin.taxonomy');
            Route::get('/nguoi-dung', Admin\UserManagement::class)  ->name('admin.users');
            Route::get('/audit',      Admin\AuditLogs::class)        ->name('admin.audit');
        });
});

require __DIR__.'/auth.php';
