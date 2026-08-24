<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bai_thi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->cascadeOnDelete();
            $table->string('ten_bai_thi');                                          // Tên bài thi
            $table->enum('che_do', ['ngau_nhien', 'theo_do_kho', 'ty_le_tuy_chon'])->default('ngau_nhien'); // Chế độ thi
            $table->json('cau_hinh_ma_tran')->nullable();                           // Cấu hình ma trận đề
            $table->unsignedInteger('so_cau_hoi');                                  // Số câu hỏi
            $table->unsignedInteger('thoi_gian_phut');                              // Thời gian (phút)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bai_thi');
    }
};
