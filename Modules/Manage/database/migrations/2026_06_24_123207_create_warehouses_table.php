<?php

use App\Models\Place;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Manage\Models\Warehouse;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Миграция для складов (например, "Бар", "Склад 1", "Кухня", "Зал")
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 2. Миграция для инвентаря
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Warehouse::class);
            $table->foreignIdFor(Place::class);
            $table->string('name'); // Наименование
            $table->text('serial_number')->nullable();
            $table->integer('quantity')->default(1); // Количество
            $table->integer('price')->default(0); // Цена за штуку
            $table->text('comment')->nullable(); // Комментарий
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('inventory_items');
    }
};
