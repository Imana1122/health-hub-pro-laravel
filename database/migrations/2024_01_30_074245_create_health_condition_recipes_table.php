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
        Schema::create('health_condition_recipes', function (Blueprint $table) {
            $table->foreignUuid('recipe_id')->references('id')->on('recipes')->onDelete('cascade');

            $table->foreignUuid('health_condition_id')->references('id')->on('health_conditions')->onDelete('cascade');

            $table->integer('status')->default(1);

            $table->primary(['recipe_id', 'health_condition_id']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_condition_recipes');
    }
};
