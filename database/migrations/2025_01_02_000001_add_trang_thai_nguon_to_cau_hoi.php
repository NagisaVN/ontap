<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            // Trạng thái duyệt: câu hỏi từ OCR phải qua teacher duyệt
            $table->enum('trang_thai', ['cho_duyet', 'da_duyet', 'tu_choi'])
                  ->default('da_duyet')
                  ->after('cau_hoi_goc_id');

            // Nguồn gốc câu hỏi
            $table->enum('nguon', ['thu_cong', 'ocr', 'ai_sinh'])
                  ->default('thu_cong')
                  ->after('trang_thai');

            // Index để lọc câu hỏi đã duyệt nhanh
            $table->index(['trang_thai', 'nguon']);
        });
    }

    public function down(): void
    {
        Schema::table('cau_hoi', function (Blueprint $table) {
            $table->dropIndex(['trang_thai', 'nguon']);
            $table->dropColumn(['trang_thai', 'nguon']);
        });
    }
};
