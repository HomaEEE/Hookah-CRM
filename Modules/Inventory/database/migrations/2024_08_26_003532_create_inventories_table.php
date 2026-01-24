<?php

use App\Models\Place;
use App\Models\Storage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Inventory\Models\Item;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Storage::class);
            $table->foreignIdFor(Place::class);
            $table->string('serial_number')->unique()->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('qty')->default(0); // Текущий общий остаток
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Item::class);
            $table->integer('qty'); // +10 или -8
            $table->string('type')->nullable(); // Приход/Расход/Перемещение
            $table->timestamp('last_audit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('inventory_transactions');
    }
};
