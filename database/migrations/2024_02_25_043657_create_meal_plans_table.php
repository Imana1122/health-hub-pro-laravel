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
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('breakfast')->constrained('recipes')->onDelete('cascade');
            $table->foreignUuid('lunch')->constrained('recipes')->onDelete('cascade');
            $table->foreignUuid('snack')->constrained('recipes')->onDelete('cascade');
            $table->foreignUuid('dinner')->constrained('recipes')->onDelete('cascade');
            $table->double('calories');
            $table->double('carbohydrates');
            $table->double('protein');
            $table->double('total_fat');
            $table->double('saturated_fat');
            $table->double('sodium');
            $table->double('sugar');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
