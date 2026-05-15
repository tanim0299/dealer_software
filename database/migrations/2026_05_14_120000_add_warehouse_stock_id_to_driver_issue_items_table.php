<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('driver_issue_items', function (Blueprint $table) {
            $table->foreignId('warehouse_stock_id')
                ->nullable()
                ->after('product_id')
                ->constrained('ware_house_stocks')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('driver_issue_items', function (Blueprint $table) {
            $table->dropForeign(['warehouse_stock_id']);
        });
    }
};
