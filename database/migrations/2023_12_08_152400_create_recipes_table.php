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
        Schema::create('recipes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug');
            $table->string('description')->nullable();
            $table->string('tags')->nullable();
            $table->foreignUuid('cuisine_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('meal_type_id')->constrained()->onDelete('cascade');
            $table->double('calories', 10, 2)->nullable();
            $table->double('total_fat', 10, 2)->nullable();
            $table->double('saturated_fat', 10, 2)->nullable();
            $table->double('sodium', 10, 2)->nullable();
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
