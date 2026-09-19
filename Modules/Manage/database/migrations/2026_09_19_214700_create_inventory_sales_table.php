<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Manage\Enums\StateEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('inventory_sales')) {
            Schema::create('inventory_sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventory_item_id')
                    ->constrained('inventory_items')
                    ->cascadeOnDelete();
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
                $table->integer('quantity')->default(1);
                $table->integer('price')->default(0);
                $table->json('serial_numbers')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Migrate existing sales data from inventory_items
        $existingSoldItems = DB::table('inventory_items')
            ->where('sold_quantity', '>', 0)
            ->get();

        foreach ($existingSoldItems as $item) {
            DB::table('inventory_sales')->insert([
                'inventory_item_id' => $item->id,
                'quantity' => (int) $item->sold_quantity,
                'price' => (int) ($item->sold_price ?? 0),
                'serial_numbers' => null,
                'notes' => 'Перенос из предыдущей продажи',
                'created_at' => $item->updated_at ?? now(),
                'updated_at' => $item->updated_at ?? now(),
            ]);

            $remainingQuantity = max(0, (int) $item->quantity - (int) $item->sold_quantity);
            $newState = $remainingQuantity > 0 ? StateEnum::ToSell->value : StateEnum::Sold->value;

            DB::table('inventory_items')
                ->where('id', $item->id)
                ->update([
                    'quantity' => $remainingQuantity,
                    'state' => $newState,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_sales');
    }
};
