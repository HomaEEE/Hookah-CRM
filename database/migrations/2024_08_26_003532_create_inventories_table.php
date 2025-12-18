<?php

use App\Models\Place;
use App\Models\Storage;
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

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Storage::class);
            $table->foreignIdFor(Place::class);
            $table->string('name'); // название инвентаря
            $table->integer('ownership'); // форма владения
            $table->string('serial_number'); // форма владения
            $table->decimal('unit_price', 12, 2)->nullable(); // цена за единицу
            $table->integer('quantity')->default(0); // текущее количество
            $table->timestamps(); // дата добавления и обновления
        });

        Schema::create('inventory_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Storage::class);
            $table->date('audit_date'); // дата проведения инвентаризации
            $table->decimal('loss_cost', 12, 2)->default(0); // общая сумма недостачи
            $table->timestamps();
        });

        Schema::create('inventory_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_audit_id')->constrained('inventory_audits')->cascadeOnDelete();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->integer('previous_qty'); // до инвентаризации
            $table->integer('current_qty');  // после инвентаризации
            $table->decimal('loss_cost', 12, 2)->default(0); // стоимость недостачи
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
