<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Добавление индексов для оптимизации поиска и кеширования
        Schema::table('users', function (Blueprint $table) {
            $table->index(['id', 'name', 'wage_id']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->index(['id', 'place_id']);
        });

        Schema::table('wages', function (Blueprint $table) {
            $table->index(['id', 'place_id']);
        });

        Schema::table('shift_users', function (Blueprint $table) {
            $table->index(['shift_id', 'user_id']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['id', 'category_id']);
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->index(['id']);
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->index(['id', 'place_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['id', 'product_id', 'place_id']);
        });

        Schema::table('reqvizites', function (Blueprint $table) {
            $table->index(['id', 'place_id']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->index(['id', 'place_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаление индексов при откате миграции

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropIndex(['place_id', 'user_id']);
        });

        Schema::table('wages', function (Blueprint $table) {
            $table->dropIndex(['place_id']);
        });

        Schema::table('shift_users', function (Blueprint $table) {
            $table->dropIndex(['shift_id', 'user_id', 'wage_id']);
        });
    }
};
