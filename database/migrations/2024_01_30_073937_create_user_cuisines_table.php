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
        Schema::create('user_cuisines', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->uuid('cuisine_id');
            $table->foreign('cuisine_id')->references('id')->on('cuisines')->onDelete('cascade');

            $table->integer('status')->default(1);

            $table->primary(['user_id', 'cuisine_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_cuisines');
    }
};
