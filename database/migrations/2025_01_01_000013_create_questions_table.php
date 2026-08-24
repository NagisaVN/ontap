<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cau_hoi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chuong_id')->constrained('chuong')->cascadeOnDelete();
            $table->text('noi_dung');                                          // Nội dung câu hỏi
            $table->string('hinh_anh')->nullable();                            // Hình ảnh (URL)
            $table->enum('do_kho', ['de', 'trung_binh', 'kho'])->default('trung_binh'); // Độ khó
            $table->text('giai_thich')->nullable();                            // Giải thích đáp án
            $table->boolean('do_ai_tao')->default(false);                      // Do AI tạo?
            $table->foreignId('cau_hoi_goc_id')                               // Câu hỏi gốc (biến thể AI)
                  ->nullable()
                  ->constrained('cau_hoi')
                  ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cau_hoi');
    }
};
