<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chuong', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mon_hoc_id')->constrained('mon_hoc')->cascadeOnDelete();
            $table->string('ten');                         // Tên chương
            $table->unsignedInteger('thu_tu')->default(0); // Thứ tự
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chuong');
    }
};
