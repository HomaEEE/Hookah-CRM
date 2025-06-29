<?php

use App\Models\Place;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegraph_chats', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id');
            $table->foreignIdFor(Place::class);
            $table->string('name')->nullable();
            $table->string('user_type')->nullable(); // 'guest' или 'user'
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('lang')->default('ua');
            $table->foreignId('telegraph_bot_id')->constrained('telegraph_bots')->cascadeOnDelete();
            $table->unique(['chat_id', 'telegraph_bot_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegraph_chats');
    }
};
