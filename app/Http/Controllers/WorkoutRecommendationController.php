<?php

namespace App\Http\Controllers;

use App\Models\CustomizedWorkout;
use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutRecommendationController extends Controller
{
    public function getWorkoutRecommendations(Request $request){
        $workouts = Workout::orderBy('name','ASC')->paginate(5);


        return response()->json([
            'status' => true,
            'data' => $workouts
        ]);
    }

    public function getWorkoutDetails(Request $request,$id){
        $type = $request->get('type');
        if($type == 'customized'){
            $workout = CustomizedWorkout::where('id',$id)->first();

        }else{
            $workout = Workout::where('id',$id)->first();

        }
        $exercises = Exercise::all()->keyBy('id');


        $workout->exercises = collect($workout->exercises)->map(function ($exerciseId) use ($exercises) {
            return $exercises->get(intval($exerciseId));
        });

        return response()->json([
            'status' => true,
            'data' => $workout
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
