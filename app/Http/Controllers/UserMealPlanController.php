<?php

namespace App\Http\Controllers;

use App\Models\MealType;
use App\Models\Recipe;
use App\Models\UserMealPlan;
use App\Models\UserProfile;
use App\Models\WeightPlan;
use Illuminate\Http\Request;

class UserMealPlanController extends Controller
{
    public function mealPlans(){

        // Assuming $userProfile contains the user's profile data

        $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();
        $userGoal = WeightPlan::where('id',$userProfile->weight_plan_id)->first()->slug;

        // Step 2: Calculate BMR
        $bmr = $this->calculateBMR($userProfile);

        // Step 3: Adjust BMR for Activity Level to get TDEE
        $tdee = $this->calculateTDEE($bmr, $userProfile->activity_level);

        // Step 4: Set Calorie Goals based on user's goal (e.g., weight loss, muscle gain)
        $calorieGoal = $this->calculateCalorieGoal($tdee, $userGoal);

        // Step 5: Calculate Macronutrient Requirements based on predefined ratios
        $macronutrientGoals = $this->calculateMacronutrientGoals($calorieGoal, $userGoal);

        // Step 6: Adjust for Individual Needs (optional)

        // Step 7: Calculate Sodium and Sugar Intake limits
        $sodiumLimit = $this->calculateSodiumLimit();
        if($userProfile->gender == 'female'){
            $sugarLimit = $this->calculateSugarLimitForWomen();

        }else{
            $sugarLimit = $this->calculateSugarLimitForMen();

        }

        // Step 8: Store calculated goals in user's profile or a separate table
        $userMealPlan = UserMealPlan::create([
            'calories' => $calorieGoal,
            'protein' => $macronutrientGoals['protein'],
            'carbohydrates' => $macronutrientGoals['carbohydrate'],
            'total_fat' => $macronutrientGoals['fat'],
            'sodium' => $sodiumLimit,
            'sugar' => $sugarLimit,
        ]);

        // Get all recipes and meal types
        $recipes = Recipe::with('meal_type')->get();
        $mealTypes = MealType::all();

        // Define the number of meal plans you want to generate
        $numMealPlans = 10;
        $mealPlansList = [];

        // Function to check if the current combination of recipes meets the calorie goal
        function isCalorieGoalMet($mealPlan, $calorieGoal) {
            $totalCalories = array_sum(array_map(function ($recipe) {
                return $recipe->calories;
            }, $mealPlan));
            return $totalCalories < $calorieGoal;
        }

        // Backtracking function to generate meal plans
        function generateMealPlan($shuffledRecipes, $mealTypes, $calorieGoal, $mealPlansList, $mealPlan = [], $index = 0) {
            // Base case: if all meal types are used
            if ($index === count($mealTypes)) {
                if (isCalorieGoalMet($mealPlan, $calorieGoal)) {
                    $mealPlansList[] = $mealPlan;
                }
                return;
            }

            $mealType = $mealTypes[$index];
            foreach ($shuffledRecipes as $recipe) {
                if ($recipe->meal_type_id === $mealType->id) {
                    // Choose the recipe
                    $mealPlan[$mealType->name] = $recipe;
                    // Recurse to the next meal type
                    generateMealPlan($shuffledRecipes, $mealTypes, $calorieGoal, $mealPlansList, $mealPlan, $index + 1);
                    // Remove the recipe (backtrack)
                    unset($mealPlan[$mealType->name]);
                }
            }
        }

        // Generate multiple meal plans
        for ($i = 0; $i = $numMealPlans; $i++) {
            // Shuffle recipes once outside the loop
            $shuffledRecipes = $recipes->shuffle();
            generateMealPlan($shuffledRecipes, $mealTypes, $calorieGoal, $mealPlansList);
        }



        // Get all recipes and meal types
        $recipes = Recipe::with('meal_type')->get();
        $mealTypes = MealType::all();

        // Define the number of meal plans you want to generate
        $numMealPlans = count($recipes);
        $mealPlansList = [];



        for ($i = 0; $i < $numMealPlans; $i++) {
            $mealPlan = [];

            // Shuffle recipes once outside the loop
            $shuffledRecipes = $recipes->shuffle();

            // Iterate over meal types
            foreach ($mealTypes as $mealType) {
                $currentCalories = 0;
                $currentProtein = 0;
                $currentFat = 0;

                // Iterate over shuffled recipes
                foreach ($shuffledRecipes as $recipe) {
                    // Check if adding the recipe exceeds the goals
                    if (
                        $currentCalories + $recipe->calories <= $calorieGoal &&
                        $currentProtein + $recipe->protein <= $macronutrientGoals['protein'] &&
                        $currentFat + $recipe->fat <= $macronutrientGoals['fat'] &&
                        $recipe->meal_type_id === $mealType->id
                    ) {
                        // Add the recipe to the meal plan
                        $mealPlan[$mealType->name][] = $recipe;

                        // Update current totals
                        $currentCalories += $recipe->calories;
                        $currentProtein += $recipe->protein;
                        $currentFat += $recipe->fat;
                    }

                    // Break if the goals are met for the current meal type
                    if (
                        $currentCalories >= $calorieGoal &&
                        $currentProtein >= $macronutrientGoals['protein'] &&
                        $currentFat >= $macronutrientGoals['fat']
                    ) {
                        break;
                    }
                }
            }
        }


        // Return the list of meal plans
        return response()->json(['status'=>true,'data'=>$mealPlansList]);
    }



    function calculateSugarLimitForMen() {
        // Define sugar limit based on dietary guidelines for men
        return 36; // Example: Daily sugar limit in grams for men
    }

    function calculateSugarLimitForWomen() {
        // Define sugar limit based on dietary guidelines for women
        return 25; // Example: Daily sugar limit in grams for women
    }

        // Step 2: Calculate BMR
    function calculateBMR($userProfile) {
        $bmr = 0;
        if ($userProfile->gender === 'male') {
            $bmr = 10 * $userProfile->weight + 6.25 * $userProfile->height - 5 * $userProfile->age + 5;
        } elseif ($userProfile->gender === 'female') {
            $bmr = 10 * $userProfile->weight + 6.25 * $userProfile->height - 5 * $userProfile->age - 161;
        }
        return $bmr;
    }

    // Step 3: Adjust BMR for Activity Level to get TDEE
    function calculateTDEE($bmr, $activityLevel) {
        $activityFactors = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
            'extra_active' => 1.9,
        ];
        return $bmr * $activityFactors[$activityLevel];
    }

    // Step 4: Set Calorie Goals based on user's goal
    function calculateCalorieGoal($tdee, $userGoal) {
        switch ($userGoal) {
            case 'muscle-gain':
                return $tdee + 500; // Example: surplus of 500 calories for muscle gain
            case 'weight-loss':
                return $tdee - 500; // Example: deficit of 500 calories for weight loss
            case 'fat-loss':
                return $tdee - 750; // Example: deficit of 750 calories for fat loss
            case 'maintain-weight':
            default:
                return $tdee; // Maintain weight
        }
    }

    // Step 5: Calculate Macronutrient Requirements based on predefined ratios
    function calculateMacronutrientGoals($calorieGoal, $goalType) {
        // Define macronutrient ratios based on goal type
        $macronutrientRatios = [
            'muscle-gain' => ['protein' => 0.3, 'carbohydrate' => 0.5, 'fat' => 0.2],
            'weight-loss' => ['protein' => 0.35, 'carbohydrate' => 0.4, 'fat' => 0.25],
            'fat-loss' => ['protein' => 0.4, 'carbohydrate' => 0.3, 'fat' => 0.3],
            'maintain-weight' => ['protein' => 0.25, 'carbohydrate' => 0.45, 'fat' => 0.3],
        ];

        // Calculate macronutrient goals
        $macronutrientGoals = [];
        foreach ($macronutrientRatios[$goalType] as $macronutrient => $ratio) {
            $macronutrientGoals[$macronutrient] = $calorieGoal * $ratio;
        }
        return $macronutrientGoals;
    }

    // Step 7: Calculate Sodium and Sugar Intake limits
    function calculateSodiumLimit() {
        // Define sodium limit based on dietary guidelines
        return 2300; // Example: Daily sodium limit in milligrams
    }


}
