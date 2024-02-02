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
        Schema::table('recipe_categories', function (Blueprint $table) {
            // Add an image column to recipe_categories table
            $table->string('image')->nullable()->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipe_categories', function (Blueprint $table) {
            // Drop the image column if the migration needs to be reversed
            $table->dropColumn('image');
        });
    }
};
