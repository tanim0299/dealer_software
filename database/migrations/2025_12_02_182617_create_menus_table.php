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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->integer('sl')->nullable();
            $table->foreignId('menu_section_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('menus')->onDelete('cascade');
            $table->string('name');
            $table->string('system_name')->unique();
            $table->string('route')->nullable();
            $table->string('slug')->nullable();
            $table->integer('type')->default(3)->comment('1=Parent, 2=Child, 3=Single'); // 1=Parent, 2=Child, 3=Single
            $table->integer('status')->default(1)->comment('0=Inactive, 1=Active'); // 0=Inactive, 1=Active
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
