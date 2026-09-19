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
        if (! Schema::hasTable('debtors')) {
            Schema::create('debtors', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('place_id')->nullable();
                $table->foreign('place_id')->references('id')->on('places')->nullOnDelete();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('inventory_sales', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_sales', 'guest_id')) {
                $table->dropConstrainedForeignId('guest_id');
            }

            if (! Schema::hasColumn('inventory_sales', 'debtor_id')) {
                $table->foreignId('debtor_id')
                    ->nullable()
                    ->after('is_debt')
                    ->constrained('debtors')
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
            if (Schema::hasColumn('inventory_sales', 'debtor_id')) {
                $table->dropConstrainedForeignId('debtor_id');
            }
        });

        Schema::dropIfExists('debtors');
    }
};
