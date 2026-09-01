<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('total_hours', 5, 2)->nullable()->after('amount');
            $table->decimal('used_hours', 5, 2)->default(0)->after('total_hours');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['total_hours', 'used_hours']);
        });
    }
};