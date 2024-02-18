<?php

use App\Models\Ingredient;
use App\Models\Supply;
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
        Schema::create('supply_item', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supply::class);
            $table->foreignIdFor(Ingredient::class);
            $table->integer('qty')->default(0);
            $table->integer('sum')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supply_item');
    }
};
