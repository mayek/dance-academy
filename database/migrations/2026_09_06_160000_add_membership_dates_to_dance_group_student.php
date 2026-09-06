<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dance_group_student', function (Blueprint $table) {
            $table->date('joined_at')->nullable()->after('student_id');
            $table->date('left_at')->nullable()->after('joined_at');
        });

        \Illuminate\Support\Facades\DB::table('dance_group_student')
            ->whereNull('joined_at')
            ->update(['joined_at' => \Illuminate\Support\Facades\DB::raw('DATE(created_at)')]);
    }

    public function down(): void
    {
        Schema::table('dance_group_student', function (Blueprint $table) {
            $table->dropColumn(['joined_at', 'left_at']);
        });
    }
};