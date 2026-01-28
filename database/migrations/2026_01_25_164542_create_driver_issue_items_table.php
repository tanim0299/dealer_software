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
        Schema::create('driver_issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->double('issue_qty');
            $table->double('sold_qty')->default(0);
            $table->double('return_qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_issue_items');
    }
};
