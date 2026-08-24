<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('luot_thi', function (Blueprint $table) {
            // Anti-cheat tracking
            $table->unsignedTinyInteger('so_lan_roi_tab')->default(0)->after('ket_thuc_luc')
                  ->comment('Số lần student rời khỏi tab trong khi thi');
            $table->boolean('nop_tre')->default(false)->after('so_lan_roi_tab')
                  ->comment('Nộp bài sau khi hết giờ cho phép');
        });
    }

    public function down(): void
    {
        Schema::table('luot_thi', function (Blueprint $table) {
            $table->dropColumn(['so_lan_roi_tab', 'nop_tre']);
        });
    }
};
