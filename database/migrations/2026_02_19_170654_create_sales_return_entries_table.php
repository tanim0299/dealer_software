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
        Schema::create('sales_return_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_ledger_id')->references('id')->on('sales_return_ledgers');
            $table->foreignId('sales_entry_id')->references('id')->on('sales_entries');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->double('return_qty',11,2)->default(0);
            $table->double('sale_price',11,2)->nullable();
            $table->double('purchase_price',11,2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_entries');
    }
};
