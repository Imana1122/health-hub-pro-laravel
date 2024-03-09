<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutRecommendationController extends Controller
{
    public function getWorkoutRecommendations(Request $request){
        $workouts = Workout::orderBy('name','ASC')->paginate(2);

        // Fetch all exercises
        $exercises = Exercise::all()->keyBy('id');

        // Manipulate the data to replace exercise IDs with exercise objects
        $workouts->each(function ($workout) use ($exercises) {
            $workout->exercises = collect($workout->exercises)->map(function ($exerciseId) use ($exercises) {
                return $exercises->get($exerciseId);
            });
        });
        return response()->json([
            'status' => true,
            'data' => $workouts
        ]);
        }

    public function getWorkoutwithExercise($id){
        $workout = Workout::where('id',$id)->first();
        // Fetch all exercises
        $exercises = Exercise::all()->keyBy('id');

        // Manipulate the data to replace exercise IDs with exercise objects

        $workout->exercises = collect($workout->exercises)->map(function ($exerciseId) use ($exercises) {
            return $exercises->get($exerciseId);
        });

        return response()->json([
            'status' => true,
            'data' => $workout
        ]);
    }
}
