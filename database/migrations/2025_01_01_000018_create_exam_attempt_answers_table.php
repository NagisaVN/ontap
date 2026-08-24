<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ket_qua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('luot_thi_id')->constrained('luot_thi')->cascadeOnDelete();
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->cascadeOnDelete();
            $table->foreignId('lua_chon_id')            // Lựa chọn đã chọn
                  ->nullable()
                  ->constrained('lua_chon')
                  ->nullOnDelete();
            $table->boolean('dung_sai')->default(false); // Đúng hay sai
            $table->text('giai_thich_ai')->nullable();   // Giải thích từ AI
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ket_qua');
    }
};
