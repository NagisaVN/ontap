<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_ke_cau_hoi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->cascadeOnDelete();
            $table->unsignedInteger('so_lan_sai')->default(0);      // Số lần làm sai
            $table->unsignedInteger('so_lan_dung')->default(0);     // Số lần làm đúng
            $table->timestamp('lan_cuoi_lam')->nullable();           // Lần cuối làm
            $table->timestamps();

            $table->unique(['nguoi_dung_id', 'cau_hoi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_ke_cau_hoi');
    }
};
