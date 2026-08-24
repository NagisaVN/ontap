<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tien_do', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('chuong_id')->constrained('chuong')->cascadeOnDelete();
            $table->unsignedInteger('tong_da_lam')->default(0);          // Tổng câu đã làm
            $table->decimal('phan_tram_thanh_thao', 5, 2)->default(0.00); // Phần trăm thành thạo
            $table->timestamps();

            $table->unique(['nguoi_dung_id', 'chuong_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tien_do');
    }
};
