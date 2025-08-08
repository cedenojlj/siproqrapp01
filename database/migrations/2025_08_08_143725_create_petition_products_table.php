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
        Schema::create('petition_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petition_id')->constrained('petitions');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity');
            $table->decimal('price');
            $table->decimal('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petition_products');
    }
};
