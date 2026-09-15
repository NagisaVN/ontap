<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_on_tap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cau_hoi_id')->constrained('cau_hoi')->cascadeOnDelete();
            $table->unsignedInteger('interval_days')->default(0);
            $table->unsignedSmallInteger('repetitions')->default(0);
            $table->decimal('ease_factor', 4, 2)->default(2.50);
            $table->timestamp('next_review_at')->index();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();

            $table->unique(['nguoi_dung_id', 'cau_hoi_id']);
            $table->index(['nguoi_dung_id', 'next_review_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_on_tap');
    }
};
