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
        Schema::create('sales_return_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_ledger_id')->references('id')->on('sales_ledgers');
            $table->string('invoice_no')->nullable();
            $table->date('date');
            $table->foreignId('customer_id')->references('id')->on('customers');
            $table->double('subtotal')->default(0);
            $table->text('note')->nullable();
            $table->foreignId('create_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_ledgers');
    }
};
