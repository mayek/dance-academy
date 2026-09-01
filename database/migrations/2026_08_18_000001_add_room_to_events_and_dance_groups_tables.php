<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('room')->nullable()->after('end_time');
        });

        Schema::table('dance_groups', function (Blueprint $table) {
            $table->string('room')->nullable()->after('teacher_id');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('room');
        });

        Schema::table('dance_groups', function (Blueprint $table) {
            $table->dropColumn('room');
        });
    }
};
