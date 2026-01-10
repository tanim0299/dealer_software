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
        Schema::create('purchase_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_ledger_id')->references('id')->on('purchase_ledgers')->onDelete('cascade');
            $table->foreignId('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->double('quantity', 15, 11);
            $table->double('final_quantity', 15, 11);
            $table->foreignId('sub_unit_id')->references('id')->on('sub_units')->onDelete('cascade');
            $table->double('unit_price', 15, 11);
            $table->double('discount', 15, 11);
            $table->double('total_price', 15, 11);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_entries');
    }
};
