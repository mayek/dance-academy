<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('made_up_for_attendance_id')
                ->nullable()
                ->after('date_of_made_up')
                ->constrained('attendances')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['made_up_for_attendance_id']);
            $table->dropColumn('made_up_for_attendance_id');
        });
    }
};