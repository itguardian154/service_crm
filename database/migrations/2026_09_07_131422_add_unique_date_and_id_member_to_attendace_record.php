<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendace_record', function (Blueprint $table) {
            $table->unique(
                ['id_member', 'tanggal'],
                'unique_member_attendance_per_day'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendace_record', function (Blueprint $table) {
            $table->dropUnique('unique_member_attendance_per_day');
        });
    }
};