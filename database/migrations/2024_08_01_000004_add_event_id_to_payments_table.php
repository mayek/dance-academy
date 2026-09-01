<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('dance_group_id')->nullable()->change();
            $table->foreignId('event_id')->nullable()->after('dance_group_id')->constrained('events')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_id');
            $table->foreignId('dance_group_id')->nullable(false)->change();
        });
    }
};
