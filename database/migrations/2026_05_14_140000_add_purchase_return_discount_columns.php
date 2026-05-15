<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_return_entries', function (Blueprint $table) {
            $table->double('discount')->default(0)->after('purchase_price');
        });

        Schema::table('purchase_return_ledgers', function (Blueprint $table) {
            $table->double('line_discount_total')->default(0)->after('subtotal');
            $table->double('order_discount')->default(0)->after('line_discount_total');
            $table->double('grand_total')->default(0)->after('order_discount');
        });

        DB::table('purchase_return_ledgers')->update([
            'line_discount_total' => 0,
            'order_discount' => 0,
            'grand_total' => DB::raw('subtotal'),
        ]);
    }

    public function down(): void
    {
        Schema::table('purchase_return_entries', function (Blueprint $table) {
            $table->dropColumn('discount');
        });

        Schema::table('purchase_return_ledgers', function (Blueprint $table) {
            $table->dropColumn(['line_discount_total', 'order_discount', 'grand_total']);
        });
    }
};
