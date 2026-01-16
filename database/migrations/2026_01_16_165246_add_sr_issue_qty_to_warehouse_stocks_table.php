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
        Schema::table('ware_house_stocks', function (Blueprint $table) {
            $table->double('sr_issue_qty')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ware_house_stocks', function (Blueprint $table) {
            $table->dropColumn('sr_issue_qty');
        });
    }
};
