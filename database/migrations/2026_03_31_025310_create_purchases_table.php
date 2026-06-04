<?php

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Manage\Models\Ingredient;
use Modules\Manage\Models\Purchase;
use Modules\Place\Models\Supplier;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Supplier::class);
            $table->string('status')->default('1');
            $table->integer('total_sum')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_item', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Purchase::class);
            $table->foreignIdFor(Ingredient::class);
            $table->integer('quantity')->default(1);
            $table->integer('price')->default(0);
            $table->integer('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_item');
        Schema::dropIfExists('purchases');
    }
};
