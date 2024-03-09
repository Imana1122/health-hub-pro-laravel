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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->double('calories')->nullable();
            $table->double('carbohydrates')->nullable();
            $table->double('protein')->nullable();
            $table->double('total_fat')->nullable();
            $table->double('saturated_fat')->nullable();
            $table->double('sodium')->nullable();
            $table->double('sugar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {

            $table->dropColumn('calories');
            $table->dropColumn('carbohydrates');
            $table->dropColumn('protein');
            $table->dropColumn('total_fat');
            $table->dropColumn('saturated_fat');
            $table->dropColumn('sodium');
            $table->dropColumn('sugar');

        });
    }
};
