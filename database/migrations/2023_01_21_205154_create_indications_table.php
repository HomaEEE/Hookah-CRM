<?php

use App\Models\Meter;
use App\Models\Place;
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
        Schema::create('indications', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Meter::class);
            $table->foreignIdFor(Place::class);
            $table->foreignIdFor(User::class);
            $table->integer('type_id');
            $table->decimal('tarif', 4, 2)->nullable()->default(0);
            $table->integer('data');
            $table->integer('paid')->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('indications');
    }
};
