<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_return_ledgers', function (Blueprint $table) {
            $table->dropForeign(['sales_ledger_id']);
        });

        Schema::table('sales_return_ledgers', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_ledger_id')->nullable()->change();
            $table->foreign('sales_ledger_id')
                ->references('id')->on('sales_ledgers')->nullOnDelete();
            $table->double('discount', 15, 4)->default(0)->after('subtotal');
            $table->double('lines_subtotal', 15, 4)->nullable()->after('discount');
            $table->string('return_mode', 24)->nullable()->after('invoice_no'); // with_invoice | without_invoice
        });

        DB::table('sales_return_ledgers')->whereNull('lines_subtotal')->update([
            'lines_subtotal' => DB::raw('subtotal'),
        ]);

        Schema::table('sales_return_entries', function (Blueprint $table) {
            $table->dropForeign(['sales_entry_id']);
        });

        Schema::table('sales_return_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_entry_id')->nullable()->change();
            $table->foreign('sales_entry_id')
                ->references('id')->on('sales_entries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales_return_entries', function (Blueprint $table) {
            $table->dropForeign(['sales_entry_id']);
        });

        Schema::table('sales_return_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_entry_id')->nullable(false)->change();
            $table->foreign('sales_entry_id')
                ->references('id')->on('sales_entries');
        });

        Schema::table('sales_return_ledgers', function (Blueprint $table) {
            $table->dropForeign(['sales_ledger_id']);
        });

        Schema::table('sales_return_ledgers', function (Blueprint $table) {
            $table->dropColumn(['discount', 'lines_subtotal', 'return_mode']);
            $table->unsignedBigInteger('sales_ledger_id')->nullable(false)->change();
            $table->foreign('sales_ledger_id')->references('id')->on('sales_ledgers');
        });
    }
};
