<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Manage\Models\Ingredient;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('ingredient_aliases')) {
            Schema::create('ingredient_aliases', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Ingredient::class)->cascadeOnDelete();
                $table->string('alias')->unique()->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('ingredient_history')) {
            Schema::create('ingredient_history', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(Ingredient::class);
                $table->integer('price');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_aliases');
        Schema::dropIfExists('ingredient_history');
    }
};
