<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_return_ledgers', function (Blueprint $table) {
            $table->double('due_adjustment', 15, 4)->default(0)->after('subtotal');
            $table->double('cash_portion', 15, 4)->default(0)->after('due_adjustment');
        });

        if (Schema::hasColumn('purchase_return_ledgers', 'return_type')) {
            DB::table('purchase_return_ledgers')->where('return_type', 2)->update([
                'due_adjustment' => DB::raw('subtotal'),
                'cash_portion' => 0,
            ]);
            DB::table('purchase_return_ledgers')->where('return_type', 1)->update([
                'due_adjustment' => 0,
                'cash_portion' => DB::raw('subtotal'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('purchase_return_ledgers', function (Blueprint $table) {
            $table->dropColumn(['due_adjustment', 'cash_portion']);
        });
    }
};
