<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\MealPlan;
use App\Models\UserMealPlan;
use App\Models\UserProfile;
use App\Models\UserRecipeLog;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $userId=auth()->user()->id;



        // Retrieve UserRecipeLog records with recipe data, including images and ingredients
        $recipeLogs = UserRecipeLog::with(['recipe'])
            ->where('user_id', $userId)
            ->whereDate('created_at',now())
            ->get();


        $caloriesSum = $recipeLogs->pluck('recipe.calories')->sum();
        $proteinSum = $recipeLogs->pluck('recipe.protein')->sum();
        $carbohydratesSum = $recipeLogs->pluck('recipe.carbohydrates')->sum();
        $totalFatSum = $recipeLogs->pluck('recipe.total_fat')->sum();
        $saturatedFatSum = $recipeLogs->pluck('recipe.saturated_fat')->sum();
        $sodiumSum = $recipeLogs->pluck('recipe.sodium')->sum();
        $sugarSum = $recipeLogs->pluck('recipe.sugar')->sum();


        $workoutLogs = WorkoutLog::with(['workout'])
        ->where('user_id', $userId)
        ->latest()
        ->take(3)
        ->get();

        $userProfile=UserProfile::where('user_id',$userId)->first();
        if(!empty($workoutLogs)){

        // Iterate over each workout log and its associated exercises
        foreach ($workoutLogs as $workoutLog) {


            $totalCaloriesBurnedForWorkout = 0;
            // Iterate over each exercise and calculate calories burned
            foreach ($workoutLog->workout->exercises as $ex) {
                $exercise = Exercise::where('id',$ex)->first();
                $calories = $exercise->metabolic_equivalent * $userProfile->weight / 60;
                $totalCaloriesBurnedForWorkout += $calories;
            }

            // Attach the total calories burned for the workout
            $workoutLog->workout->total_calories_burned = $totalCaloriesBurnedForWorkout;

        }
    }

        $userMealPlan = UserMealPlan::where('user_id',auth()->user()->id)->whereDate('created_at',now())->first();
        if(!empty($userMealPlan)){
            if($userMealPlan->meal_plan_id != null){
                $mealPlan = MealPlan::where('id',$userMealPlan->meal_plan_id)->with('breakfastRecipe.images','breakfastRecipe.meal_type','breakfastRecipe.ingredient')->with('snackRecipe.images','snackRecipe.meal_type','snackRecipe.ingredient')->with('lunchRecipe.images','lunchRecipe.meal_type','lunchRecipe.ingredient')->with('dinnerRecipe.images','dinnerRecipe.meal_type','dinnerRecipe.ingredient')->first();
                // Now you have the sum of each nutrient value
                return response()->json([
                    'status'=>true,
                    'data'=>[
                        'workoutLogs'=>$workoutLogs,
                        'mealData'=>[
                            'calories'=>$caloriesSum,
                            'protein'=>$proteinSum,
                            'carbohydrates'=>$carbohydratesSum,
                            'total_fat'=>$totalFatSum,
                            'saturated_fat'=>$saturatedFatSum,
                            'sodium'=>$sodiumSum,
                            'sugar'=>$sugarSum,
                            ],
                            'mealPlan'=>$mealPlan
                        ],


                ]);
            }
        }

        // Now you have the sum of each nutrient value
        return response()->json([
            'status'=>true,
            'data'=>[
                'workoutLogs'=>$workoutLogs,
                'mealData'=>[
                    'calories'=>$caloriesSum,
                    'protein'=>$proteinSum,
                    'carbohydrates'=>$carbohydratesSum,
                    'total_fat'=>$totalFatSum,
                    'saturated_fat'=>$saturatedFatSum,
                    'sodium'=>$sodiumSum,
                    'sugar'=>$sugarSum,
                    ],
                ]
        ]);

    }

    public function getBadges() {
        $noOfBadges = UserRecipeLog::where('user_id', auth()->user()->id)
            ->selectRaw('count(distinct date(updated_at)) as count')
            ->first()
            ->count;

        // Assuming you want badges for every 7 distinct days
        $noOfBadges = intval($noOfBadges / 7);

        return response()->json([
            'status' => true,
            'data' => $noOfBadges
        ]);
    }
}
