<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Student;
use App\Livewire\Teacher;
use App\Livewire\Admin;

// ── Redirect root ──────────────────────────────────────────
Route::redirect('/', '/login');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ── Student ────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard (redirect by role)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('super_admin') || $user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }
        return app(Student\Dashboard::class)();
    })->name('dashboard');

    // Student-only routes
    Route::middleware('role:student|teacher|super_admin')->group(function () {
        Route::get('/thi', Student\CreateExam::class)->name('student.thi');
        Route::get('/thi/{baiThi}', Student\ExamRoom::class)->name('exam.room');
        Route::get('/ket-qua/{luotThi}', Student\ExamResult::class)->name('exam.result');
        Route::get('/on-tap', Student\SpacedRepetition::class)->name('student.on-tap');
    });

    // Teacher routes
    Route::prefix('giao-vien')
        ->middleware('can:teacher')
        ->group(function () {
            Route::get('/', Teacher\Dashboard::class)->name('teacher.dashboard');
            Route::get('/cau-hoi', Teacher\QuestionManager::class)->name('teacher.questions');
            Route::get('/cho-duyet', Teacher\PendingReview::class)->name('teacher.pending');
            Route::get('/ocr-upload', Teacher\OcrUpload::class)->name('teacher.ocr');
        });

    // Admin routes
    Route::prefix('quan-tri')
        ->middleware('can:admin')
        ->group(function () {
            Route::get('/', Admin\Dashboard::class)->name('admin.dashboard');
            Route::get('/cau-truc', Admin\AdminTaxonomyManager::class)->name('admin.taxonomy');
            Route::get('/nguoi-dung', Admin\UserManagement::class)->name('admin.users');
        });
});

require __DIR__.'/auth.php';
