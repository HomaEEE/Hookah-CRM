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
        Schema::create('menus', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('name');
            $table->integer('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');

        });
        Schema::create('categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('category_name');
            $table->text('frontend_name')->nullable();
            $table->text('frontend_menu_name')->nullable();
            $table->text('frontend_desc')->nullable();
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('parent_category');
            $table->unsignedInteger('place_id');
            $table->text('image')->nullable();
            $table->text('logo')->nullable();
            $table->text('custom_class')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('visible')->default(1);
            $table->boolean('visible_menu')->default('1');
            $table->longText('description')->nullable();
            $table->longText('slug')->nullable();
            $table->text('class')->nullable();
            /// $table->foreign('parent_category')->constrained('categories')->references('category_id')->on('categories')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->cascadeOnDelete();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('product_name');
            $table->text('frontend_name')->nullable();
            $table->text('frontend_desc')->nullable();
            $table->string('sup')->nullable();

            $table->integer('product_id');
            $table->unsignedInteger('menu_category_id');
            $table->unsignedInteger('place_id');
            $table->decimal('cost', 10, 2)->nullable();                //закупка
            $table->decimal('price', 10, 2)->nullable();                //цена продажи
            $table->text('image')->nullable();
            $table->string('barcode')->nullable();
            $table->string('out')->nullable();
            $table->text('ingredients')->nullable();
            $table->boolean('visible')->default('1');
            $table->boolean('visible_menu')->default('1');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            // $table->foreign('menu_category_id')->references('category_id')->on('categories')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
