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
        Schema::table('driver_issue_items', function (Blueprint $table) {
            $table->double('purchase_price',11,12)->default(0);
            $table->double('sale_price',11,12)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_issue_items', function (Blueprint $table) {
            $table->dropColumn(['purchase_price','sale_price']);
        });
    }
};
