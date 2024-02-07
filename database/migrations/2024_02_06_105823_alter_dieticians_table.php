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
        Schema::table('dieticians', function (Blueprint $table) {
            // Add an image column to recipe_categories table
            $table->integer('approved_status')->default(0)->after('status');
            $table->integer('availability_status')->default(1)->after('approved_status');
            $table->string('image')->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dieticians', function (Blueprint $table) {
            // Drop the image column if the migration needs to be reversed
            $table->dropColumn('approved_status');
            $table->dropColumn('availability_status');
            $table->dropColumn('image');

        });
    }
};
