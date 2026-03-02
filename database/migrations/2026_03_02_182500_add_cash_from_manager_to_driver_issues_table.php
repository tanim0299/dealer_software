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
        Schema::table('driver_issues', function (Blueprint $table) {
            $table->double('cash_from_manager', 15, 2)->default(0)->after('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_issues', function (Blueprint $table) {
            $table->dropColumn('cash_from_manager');
        });
    }
};
