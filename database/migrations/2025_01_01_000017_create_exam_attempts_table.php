<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luot_thi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('bai_thi_id')->constrained('bai_thi')->cascadeOnDelete();
            $table->decimal('diem_so', 5, 2)->nullable();           // Điểm số
            $table->unsignedInteger('so_cau_dung')->default(0);     // Số câu đúng
            $table->unsignedInteger('thoi_gian_lam')->nullable()->comment('giây'); // Thời gian làm (giây)
            $table->enum('trang_thai', ['dang_lam', 'hoan_thanh', 'bo_cuoc'])->default('dang_lam'); // Trạng thái
            $table->timestamp('bat_dau_luc')->nullable();            // Bắt đầu lúc
            $table->timestamp('ket_thuc_luc')->nullable();           // Kết thúc lúc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luot_thi');
    }
};
