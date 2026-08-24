<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nganh', function (Blueprint $table) {
            $table->id();
            $table->string('ten');           // Tên ngành
            $table->text('mo_ta')->nullable(); // Mô tả
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nganh');
    }
};
