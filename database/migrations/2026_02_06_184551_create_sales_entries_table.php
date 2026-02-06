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
        Schema::create('sales_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_id')->references('id')->on('sales_ledgers')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->double('quantity',11,2)->default(0);
            $table->double('final_quantity',11,2)->default(0);
            $table->foreignId('sub_unit_id')->references('id')->on('sub_units')->onDelete('cascade');
            $table->double('sale_price')->default(0);
            $table->double('discount',11,12)->default(0);
            $table->double('purchase_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_entries');
    }
};
