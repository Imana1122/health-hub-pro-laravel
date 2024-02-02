<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutRecommendationController extends Controller
{
    public function getRecipeRecommendations(Request $request){
        $workouts = Workout::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'workouts' => $workouts
        ]);
    }
}
