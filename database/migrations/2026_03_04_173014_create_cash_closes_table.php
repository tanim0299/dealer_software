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
        Schema::create('cash_closes', function (Blueprint $table) {
            $table->id();
            $table->date('close_date')->unique();
            $table->decimal('opening_balance', 15, 2)->default(0);
            
            
            // Calculated totals
            $table->decimal('total_cash_in', 15, 2)->default(0);
            $table->decimal('total_cash_out', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            
            // User tracking
            $table->foreignId('closed_by')->constrained('users')->onDelete('restrict');
            $table->timestamp('closed_at')->useCurrent();
            $table->foreignId('reopened_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('reopened_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_closes');
    }
};
