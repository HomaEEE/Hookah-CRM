<?php

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Finance\Models\Bill;
use Modules\Finance\Models\ExpenseCategory;
use Modules\Manage\Models\Supply;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('external_api_id')->nullable();

            $table->enum('type', ['1', '2', '3', '4'])->index();

            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Place::class);

            $table->integer('sum');
            $table->tinyInteger('is_reviewed')->default(1);

            $table->foreignIdFor(ExpenseCategory::class);
            $table->foreignIdFor(Supply::class)->nullable();
            $table->foreignIdFor(Bill::class);

            $table->integer('poster')->default(0);
            $table->text('comment')->nullable();
            $table->timestamp('added_at');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
