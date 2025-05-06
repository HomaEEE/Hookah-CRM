<?php

use App\Models\Place;
use App\Models\Shift;
use App\Models\User;
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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(User::class);
            $table->integer('cash');
            $table->integer('terminal');
            $table->integer('old_shift');

            $table->integer('bar')->default(0);
            $table->integer('bulbs')->default(0);
            $table->integer('hookah')->default(0);
            $table->integer('kitchen')->default(0);

            $table->integer('tips')->default(0);
            $table->integer('expense')->default(0);
            $table->integer('income')->default(0);
            $table->integer('wages')->default(0);
            $table->integer('calc_shift');
            $table->integer('shift');
            $table->timestamps();
        });

        Schema::create('shift_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Shift::class);
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreignIdFor(User::class);
            $table->integer('wage_id');
            $table->double('days', 7, 1)->default(0.0)->nullable();
            $table->integer('wage')->default(0);
            $table->integer('procent_day');
            $table->integer('cashout');
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
        Schema::dropIfExists('shifts');
    }
};
