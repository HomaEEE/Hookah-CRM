<?php

use App\Models\Place;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Indicators\Models\Indication;
use Modules\Indicators\Models\Meter;

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
            $table->foreignIdFor(Place::class);
            $table->integer('type');
            $table->integer('tariff')->default(0);
            $table->timestamps();
        });

        Schema::create('indication', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data');
            $table->timestamps();
        });

        Schema::create('meter_indication', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignIdFor(Indication::class);
            $table->foreignIdFor(Meter::class);
            $table->integer('tariff')->default(0);
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
        Schema::dropIfExists('indication');
        Schema::dropIfExists('meter_indication');
    }
};
