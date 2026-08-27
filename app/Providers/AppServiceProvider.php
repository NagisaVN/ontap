<?php

namespace App\Providers;

use App\Repositories\Contracts\ExamAttemptRepositoryInterface;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use App\Repositories\ExamAttemptRepository;
use App\Repositories\QuestionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * Bind Repository interfaces to concrete implementations.
     */
    public function register(): void
    {
        $this->app->bind(
            QuestionRepositoryInterface::class,
            QuestionRepository::class,
        );

        $this->app->bind(
            ExamAttemptRepositoryInterface::class,
            ExamAttemptRepository::class,
        );

        // GeminiAIService dùng singleton để tái sử dụng config
        $this->app->singleton(\App\Services\GeminiAIService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('admin', fn($user) => $user->hasRole('super_admin'));
        \Illuminate\Support\Facades\Gate::define('teacher', fn($user) => $user->hasRole('super_admin') || $user->hasRole('teacher'));
        \Illuminate\Support\Facades\Gate::define('student', fn($user) => $user->hasRole('student'));
    }
}
