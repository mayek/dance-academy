<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('recorded_by')
                ->constrained('payments')->nullOnDelete();
            $table->decimal('hours_consumed', 4, 2)->default(0)->after('payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_id');
            $table->dropColumn('hours_consumed');
        });
    }
};