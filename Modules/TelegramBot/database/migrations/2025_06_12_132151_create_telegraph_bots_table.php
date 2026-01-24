<?php

use App\Models\Place;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegraph_bots', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('name')->nullable();
            $table->integer('webhook')->default(0);
            $table->foreignIdFor(Place::class);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegraph_bots');
    }
};
