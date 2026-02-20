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
        Schema::create('purchase_return_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_ledger_id')->references('id')->on('purchase_return_ledgers');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->double('return_qty')->default(0);
            $table->double('purchase_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_entries');
    }
};
