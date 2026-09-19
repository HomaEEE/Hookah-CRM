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
        Schema::table('inventory_sales', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_sales', 'is_debt')) {
                $table->boolean('is_debt')->default(false)->after('notes');
            }
            if (! Schema::hasColumn('inventory_sales', 'debtor_name')) {
                $table->string('debtor_name')->nullable()->after('is_debt');
            }
            if (! Schema::hasColumn('inventory_sales', 'paid_amount')) {
                $table->integer('paid_amount')->default(0)->after('debtor_name');
            }
            if (! Schema::hasColumn('inventory_sales', 'debt_amount')) {
                $table->integer('debt_amount')->default(0)->after('paid_amount');
            }
            if (! Schema::hasColumn('inventory_sales', 'is_debt_settled')) {
                $table->boolean('is_debt_settled')->default(false)->after('debt_amount');
            }
            if (! Schema::hasColumn('inventory_sales', 'settled_at')) {
                $table->timestamp('settled_at')->nullable()->after('is_debt_settled');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_sales', function (Blueprint $table) {
            $table->dropColumn([
                'is_debt',
                'debtor_name',
                'paid_amount',
                'debt_amount',
                'is_debt_settled',
                'settled_at',
            ]);
        });
    }
};
