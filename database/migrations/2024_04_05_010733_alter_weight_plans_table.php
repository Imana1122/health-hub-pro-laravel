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
        Schema::table('weight_plans', function (Blueprint $table) {
            $table->double('protein_ratio')->after('id');
            $table->double('carb_ratio')->after('id');
            $table->double('fat_ratio')->after('id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weight_plans', function (Blueprint $table) {
            $table->dropColumn('protein_ratio');
            $table->dropColumn('carb_ratio');
            $table->dropColumn('fat_ratio');

        });
    }
};
