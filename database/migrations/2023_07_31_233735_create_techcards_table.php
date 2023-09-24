<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\{Ingredient, Place, Techcard, Workshop};

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('techcards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(Workshop::class);
            $table->string('techcard')->index();
            $table->text('process');
            $table->time('time');

        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(Workshop::class);
            $table->string('product_name')->index();
            $table->integer('barcode')->nullable();
            $table->integer('cost')->nullable();
            $table->integer('workshop_id')->nullable();
            $table->integer('product_id');
            $table->integer('cost')->nullable();
            $table->integer('type');
            $table->string('unit');
            $table->string('price');
        });

        Schema::create('techcard_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Techcard::class);
            $table->foreignIdFor(Ingredient::class);
            $table->integer('heft');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('techcards');
        Schema::dropIfExists('techcard_components');
        Schema::dropIfExists('сomponents');
        Schema::dropIfExists('сomponent_history');
}
};
