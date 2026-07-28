<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dance_group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dance_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['dance_group_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dance_group_student');
    }
};
