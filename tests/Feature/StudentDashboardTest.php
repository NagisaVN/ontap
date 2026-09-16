<?php

use App\Enums\AttemptStatus;
use App\Livewire\Student\Dashboard;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Major;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('renders dashboard figures from the signed-in student attempts', function () {
    Role::create(['name' => 'student', 'guard_name' => 'web']);
    $student = User::factory()->create();
    $student->assignRole('student');

    $major = Major::create(['ten' => 'Khối kiểm thử']);
    $subject = Subject::create(['nganh_id' => $major->id, 'ten' => 'Toán', 'ma_mon' => 'TOAN']);
    $exam = Exam::create([
        'nguoi_dung_id' => $student->id,
        'mon_hoc_id' => $subject->id,
        'ten_bai_thi' => 'Đề kiểm thử',
        'so_cau_hoi' => 10,
        'thoi_gian_phut' => 30,
    ]);

    ExamAttempt::create([
        'nguoi_dung_id' => $student->id,
        'bai_thi_id' => $exam->id,
        'trang_thai' => AttemptStatus::HoanThanh,
        'diem_so' => 8.5,
        'so_cau_dung' => 8,
        'thoi_gian_lam' => 1800,
        'bat_dau_luc' => now()->subMinutes(30),
        'ket_thuc_luc' => now(),
    ]);

    $this->actingAs($student);

    Livewire::test(Dashboard::class)
        ->assertSee('1')
        ->assertSee('85%')
        ->assertSee('🔥 1 ngày');
});
