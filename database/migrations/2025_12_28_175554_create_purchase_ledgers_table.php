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
        Schema::create('purchase_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); 
            $table->date('purchase_date');
            $table->foreignId('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->double('total_amount', 15, 11);
            $table->double('discount', 15, 11);
            $table->double('paid_amount', 15, 11)->default(0);
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_ledgers');
    }
};
