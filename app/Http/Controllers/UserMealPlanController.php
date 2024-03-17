<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\UserMealPlan;
use App\Models\UserProfile;
use App\Models\UserRecipeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserMealPlanController extends Controller
{

    public function index(){
        $userId = auth()->user()->id;
        $userProfile = UserProfile::where('user_id',$userId)->first();


        // Define the target values
        $targetValues = [
            'calories' => $userProfile->calories,
            'carbohydrates' => $userProfile->carbohydrates,
            'protein' => $userProfile->protein,
            'total_fat' => $userProfile->total_fat,
            'sodium' => $userProfile->sodium,
            'sugar' => $userProfile->sugar,
        ];

        // Query to retrieve meal plans sorted by the sum of absolute differences from target values
        $mealPlansQuery = MealPlan::with('breakfastRecipe.images','breakfastRecipe.meal_type','breakfastRecipe.ingredient')->with('snackRecipe.images','snackRecipe.meal_type','snackRecipe.ingredient')->with('lunchRecipe.images','lunchRecipe.meal_type','lunchRecipe.ingredient')->with('dinnerRecipe.images','dinnerRecipe.meal_type','dinnerRecipe.ingredient')->select('*');

        foreach ($targetValues as $key => $value) {
            $mealPlansQuery->orderByRaw("ABS($key - $value)");
        }

        // Define the pagination limit
        $perPage = 3;

        // Paginate the results
        $closestMealPlans = $mealPlansQuery->paginate($perPage);

        return response()->json(['status'=>true,'data'=>$closestMealPlans]);
    }

    public function selectMealPlan(Request $request){
        $validator = Validator::make($request->all(), [
            "meal_plan_id" => "required",
            'created_at' => "required"
        ]);

        if ($validator->passes()) {
            $userMealPlan = UserMealPlan::where('user_id',auth()->user()->id)->whereDate('created_at',$request->created_at)->first();
            $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();
            if (empty($userMealPlan)) {
                $userMealPlan = UserMealPlan::create([
                    'user_id' => auth()->user()->id,
                    'meal_plan_id'=>$request->meal_plan_id,
                    'calories' => $userProfile->calories,

                    'carbohydrates' => $userProfile->carbohydrates,
                    'protein' => $userProfile->protein,
                    'total_fat' => $userProfile->total_fat,
                    'sodium' => $userProfile->sodium,
                    'sugar' => $userProfile->sugar,
                ]);
            } else {
                $userMealPlan->update([
                    'calories' => $userProfile->calories,

                    'carbohydrates' => $userProfile->carbohydrates,
                    'meal_plan_id'=>$request->meal_plan_id,

                    'protein' => $userProfile->protein,
                    'total_fat' => $userProfile->total_fat,
                    'saturated_fat' => $userProfile->saturated_fat,
                    'sodium' => $userProfile->sodium,
                    'sugar' => $userProfile->sugar,
                ]);
            }


            return response()->json([
                'status' => true,
                'message' => 'Meal Plan selected successfully!'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }



}
