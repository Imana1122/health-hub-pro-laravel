<?php

namespace Database\Seeders;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeightPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weightplans = array(
			array('name' => 'Lose Weight', 'slug' => 'lose-weight'),
            array('name' => 'Maintain Weight', 'slug' => 'maintain-weight'),
            array('name' => 'Gain Weight', 'slug' => 'gain-weight'),


		);

        foreach ($weightplans as &$plan) {
            // Generate a unique UUID and assign it to the 'id' key in each subarray
            $plan['id'] = Uuid::uuid4()->toString();
        }

		DB::table('weight_plans')->insert($weightplans);

    }
}
