<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bai_thi_cau_hoi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bai_thi_id')->constrained('bai_thi')->cascadeOnDelete();
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->cascadeOnDelete();
            $table->unsignedInteger('thu_tu')->default(0); // Thứ tự câu hỏi trong bài
            $table->timestamps();

            $table->unique(['bai_thi_id', 'cau_hoi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bai_thi_cau_hoi');
    }
};
