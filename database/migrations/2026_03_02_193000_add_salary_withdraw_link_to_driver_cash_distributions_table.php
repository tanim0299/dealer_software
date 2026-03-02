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
        Schema::table('driver_cash_distributions', function (Blueprint $table) {
            $table->foreignId('employee_salary_withdraw_id')
                ->nullable()
                ->after('employee_id')
                ->constrained('employee_salary_withdraws')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_cash_distributions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employee_salary_withdraw_id');
        });
    }
};
