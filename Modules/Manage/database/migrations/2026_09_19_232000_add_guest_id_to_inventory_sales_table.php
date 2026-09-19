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
            if (! Schema::hasColumn('inventory_sales', 'guest_id')) {
                $table->foreignId('guest_id')
                    ->nullable()
                    ->after('is_debt')
                    ->constrained('guests')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_sales', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_sales', 'guest_id')) {
                $table->dropConstrainedForeignId('guest_id');
            }
        });
    }
};
