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
        Schema::table('driver_closings', function (Blueprint $table) {
            $table->double('cash_from_manager', 15, 2)->default(0)->after('cash_in_hand');
            $table->double('cash_given_to_others', 15, 2)->default(0)->after('cash_from_manager');
            $table->double('driver_cash_take', 15, 2)->default(0)->after('cash_given_to_others');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_closings', function (Blueprint $table) {
            $table->dropColumn(['cash_from_manager', 'cash_given_to_others', 'driver_cash_take']);
        });
    }
};
