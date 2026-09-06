<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ── Cấu hình tin tưởng load balancer của Render để nhận đúng HTTPS ──
        $middleware->trustProxies(at: '*');

        // Spatie Laravel Permission middleware aliases (Laravel 11 requires explicit registration)
        $middleware->alias([
            'role'                => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'          => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission'  => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // ── Khi bị 403 (sai role) → redirect về dashboard đúng role ──
        // Thay vì hiện trang lỗi 403, user được đưa về đúng nơi của mình
        $exceptions->render(function (\Throwable $e, Request $request) {
            $is403 = ($e instanceof HttpException && $e->getStatusCode() === 403)
                  || ($e instanceof \Spatie\Permission\Exceptions\UnauthorizedException);

            if ($is403 && !$request->is('api/*') && !$request->expectsJson()) {
                if (auth()->check()) {
                    $user = auth()->user();
                    $destination = match (true) {
                        $user->hasRole('super_admin') => route('admin.dashboard'),
                        $user->hasRole('teacher')     => route('teacher.dashboard'),
                        default                       => route('student.dashboard'),
                    };
                    return redirect($destination)
                        ->with('warning', 'Bạn không có quyền truy cập trang đó.');
                }
                // Guest: redirect về login
                return redirect()->route('login');
            }

            // Trả về null = dùng xử lý mặc định của Laravel
            return null;
        });
    })->create();
