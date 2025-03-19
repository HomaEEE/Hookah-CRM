<?php

use App\Models\Supplier;
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
        Schema::create('supplier_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Supplier::class);
            $table->string('IBAN');
            $table->string('INN')->nullable();
            $table->string('own')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_details');
    }
};
