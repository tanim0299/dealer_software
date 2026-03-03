<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('permissions')->insert([
            // Bank Account permissions
            ['name' => 'Bank Account create', 'guard_name' => 'web', 'parent' => 'Bank Account', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Account view', 'guard_name' => 'web', 'parent' => 'Bank Account', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Account edit', 'guard_name' => 'web', 'parent' => 'Bank Account', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Account destroy', 'guard_name' => 'web', 'parent' => 'Bank Account', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Account show', 'guard_name' => 'web', 'parent' => 'Bank Account', 'created_at' => now(), 'updated_at' => now()],
            
            // Bank Transaction permissions
            ['name' => 'Bank Transaction create', 'guard_name' => 'web', 'parent' => 'Bank Transaction', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transaction view', 'guard_name' => 'web', 'parent' => 'Bank Transaction', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transaction edit', 'guard_name' => 'web', 'parent' => 'Bank Transaction', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transaction destroy', 'guard_name' => 'web', 'parent' => 'Bank Transaction', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bank Transaction show', 'guard_name' => 'web', 'parent' => 'Bank Transaction', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->where('parent', 'Bank Account')->delete();
        DB::table('permissions')->where('parent', 'Bank Transaction')->delete();
    }
};
