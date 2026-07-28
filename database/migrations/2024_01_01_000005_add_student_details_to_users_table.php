<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->date('date_of_birth')->nullable()->after('last_name');
            $table->string('pesel')->nullable()->unique()->after('date_of_birth');
            $table->string('phone_number')->nullable()->after('pesel');
            $table->string('parent_phone_number')->nullable()->after('phone_number');
            $table->string('tournament_group')->nullable()->after('parent_phone_number');
            $table->text('notes')->nullable()->after('tournament_group');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'date_of_birth', 'pesel',
                'phone_number', 'parent_phone_number', 'tournament_group', 'notes',
            ]);
        });
    }
};
