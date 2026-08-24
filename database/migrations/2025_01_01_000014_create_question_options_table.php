<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lua_chon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->cascadeOnDelete();
            $table->text('noi_dung');                          // Nội dung lựa chọn
            $table->boolean('la_dap_an')->default(false);      // Là đáp án đúng?
            $table->unsignedInteger('thu_tu')->default(0);     // Thứ tự hiển thị
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lua_chon');
    }
};
