<?php

use App\Models\Place;
use App\Models\Workshop;
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
        Schema::create('techcards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(Workshop::class);
            $table->string('techcard')->index();
            $table->text('process')->nullable();
            $table->json('ingredients')->nullable();

        });

        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(Workshop::class);
            $table->string('product_name')->index();
            $table->string('barcode')->nullable();
            $table->integer('cost')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('type');
            $table->string('unit');
            $table->string('price');
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
        Schema::dropIfExists('ingredients');
    }
};
