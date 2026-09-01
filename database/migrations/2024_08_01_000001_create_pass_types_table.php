<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pass_types', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['monthly', 'single']);
            $table->unsignedTinyInteger('duration_months')->nullable();
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pass_types');
    }
};
