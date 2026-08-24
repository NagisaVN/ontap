<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mon_hoc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nganh_id')->constrained('nganh')->cascadeOnDelete();
            $table->string('ten');              // Tên môn học
            $table->string('ma_mon')->unique(); // Mã môn
            $table->text('mo_ta')->nullable();  // Mô tả
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mon_hoc');
    }
};
