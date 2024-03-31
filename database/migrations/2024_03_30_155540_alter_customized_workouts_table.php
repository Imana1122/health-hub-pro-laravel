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
        Schema::table('customized_workouts', function (Blueprint $table) {
            $table->integer('no_of_ex_per_set')->default(3)->before('created_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customized_workouts', function (Blueprint $table) {
            $table->dropColumn('no_of_ex_per_set');

        });
    }
};
