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
        Schema::create('sales_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->date('date');
            $table->time('time');
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->double('subtotal',11,12)->default(0);
            $table->double('discount',11,12)->default(0);
            $table->double('paid',11,12)->default(0);
            $table->text('note')->nullable();
            $table->string('slip_image')->nullable();
            $table->foreignId('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->references('id')->on('drivers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_ledgers');
    }
};
