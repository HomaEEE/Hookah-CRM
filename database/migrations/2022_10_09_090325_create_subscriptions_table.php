<?php

use App\Models\{Place};
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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->string('name');
            $table->integer('category_id');
            $table->decimal('price', 10, 2)->default(0.0);
            $table->string('notes')->nullable();
            $table->integer('period');
            $table->timestamp('next_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};
