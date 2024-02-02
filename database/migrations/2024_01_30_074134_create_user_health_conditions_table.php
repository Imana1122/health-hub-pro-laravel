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
        Schema::create('user_health_conditions', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('health_condition_id');
            $table->foreign('health_condition_id')->references('id')->on('health_conditions')->onDelete('cascade');

            $table->integer('status')->default(1);

            $table->primary(['user_id', 'health_condition_id']);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_health_conditions');
    }
};
