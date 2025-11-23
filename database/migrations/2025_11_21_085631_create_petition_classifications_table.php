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
        Schema::create('petition_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petition_id')->constrained('petitions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('classification_id')->constrained('classifications')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petition_classifications');
    }
};
