<?php

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
        Schema::create('meters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
            $table->integer('room_id')->unsigned()->default(0);
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->integer('type');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('meters_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meter_id')->unsigned();
            $table->foreign('meter_id')->references('id')->on('meters')->onDelete('cascade');
            $table->integer('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meters');
        Schema::dropIfExists('meters_data');
    }
};
