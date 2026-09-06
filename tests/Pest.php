<?php

/**
 * Pest.php — Global test configuration for SmartPrep
 *
 * This file is loaded by Pest before running any tests.
 * It configures global uses, datasets, helpers, and test setup.
 */

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Global "uses" — All Feature tests extend Laravel's TestCase
| and use RefreshDatabase to reset DB between tests
|--------------------------------------------------------------------------
*/
uses(TestCase::class, RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Global "uses" — Unit tests just use TestCase (no DB needed)
|--------------------------------------------------------------------------
*/
uses(TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Global beforeEach — Seed required data (roles, permissions)
| Spatie Permission requires roles to exist before assigning them
|--------------------------------------------------------------------------
*/
beforeEach(function () {
    // ── Spatie Permission: xóa cache roles/permissions ──────────────────
    // Bắt buộc khi dùng RefreshDatabase — không xóa cache thì assignRole()
    // vẫn đọc danh sách roles cũ từ cache và ném RoleDoesNotExist exception
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    // ── Tạo các roles cần thiết ─────────────────────────────────────────
    foreach (['student', 'teacher', 'super_admin'] as $roleName) {
        \Spatie\Permission\Models\Role::firstOrCreate([
            'name'       => $roleName,
            'guard_name' => 'web',
        ]);
    }
});
