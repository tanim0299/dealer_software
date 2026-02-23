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
        Schema::create('driver_closings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->references('id')->on('drivers');
            $table->date('date');
            $table->double('cash_sales',11,2)->default('0');
            $table->double('total_collection',11,2)->default('0');
            $table->double('total_return',11,2)->default('0');
            $table->double('total_expense',11,2)->default('0');
            $table->double('cash_in_hand',11,2)->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_closings');
    }
};
