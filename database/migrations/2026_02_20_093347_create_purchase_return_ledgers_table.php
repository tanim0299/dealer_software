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
        Schema::create('purchase_return_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->references('id')->on('suppliers');
            $table->integer('return_type')->default(0);
            $table->double('subtotal')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_return_ledgers');
    }
};
