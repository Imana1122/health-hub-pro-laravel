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
        Schema::create('allergen_recipes', function (Blueprint $table) {
            $table->foreignUuid('recipe_id')->references('id')->on('recipes')->onDelete('cascade');

            $table->foreignUuid('allergen_id')->references('id')->on('allergens')->onDelete('cascade');

            $table->integer('status')->default(1);

            $table->primary(['recipe_id', 'allergen_id']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergen_recipes');
    }
};
