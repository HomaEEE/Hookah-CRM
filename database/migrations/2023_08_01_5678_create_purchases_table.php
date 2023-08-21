<?php

use App\Models\{Place, Supplier, User, ingredient, Purchase};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if ( ! Schema::hasTable('purchases'))
            {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignIdFor(User::class);
                $table->foreignIdFor(Place::class);
                $table->foreignIdFor(Supplier::class);
                $table->tinyInteger('status')->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        };

        if ( ! Schema::hasTable('purchase_ingredients'))
        {
        Schema::create('purchase_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Purchase::class);
            $table->foreignIdFor(ingredient::class);
            $table->integer('qty');
            $table->timestamp('created_at')->useCurrent();
        });
    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('purchase_ingredients');
    }
};
