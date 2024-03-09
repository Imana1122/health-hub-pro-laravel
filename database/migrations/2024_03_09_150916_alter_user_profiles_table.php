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
            $table->double('bmi')->after('sugar');
            $table->integer('notification')->after('bmi');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dieticians', function (Blueprint $table) {
            $table->dropColumn('notification');
            $table->dropColumn('bmi');

        });
    }
};
