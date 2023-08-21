<?php

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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Place::class);
            $table->integer('expense_category_id')->nullable();
            $table->integer('sum')->nullable();
            $table->integer('type_pay')->nullable();
            $table->string('comment')->nullable();
            $table->text('invoice')->nullable();
            $table->integer('poster')->default(0);
            $table->timestamp('added_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Place::class);
            $table->string('name');
            $table->integer('poster_category_id')->nullable();
            $table->string('comment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_category');
    }
};
