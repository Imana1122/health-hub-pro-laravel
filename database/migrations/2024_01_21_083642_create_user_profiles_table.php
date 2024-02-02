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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->double('height');
            $table->double('weight');
            $table->double('waist');
            $table->double('hips');
            $table->double('bust');
            $table->integer('age');

            // Add gender column with allowed values 'female' and 'male'
            $table->enum('gender', ['female', 'male']);

            $table->double('targeted_weight');
            $table->foreignUuid('weight_plan_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
