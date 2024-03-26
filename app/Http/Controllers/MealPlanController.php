<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use App\Models\Recipe;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    public function index(){
    //     set_time_limit(700);
    //     $breakfastMealTypeId='4bcd628b-2595-4c9e-ae57-2ff6e83eadba';
    //     $lunchMealTypeId ='058152c4-4894-4756-95ae-3bba523be047';
    //     $snackMealTypeId='d37900ed-e44c-468a-bfaf-533734b39155';
    //     $dinnerMealTypeId='26ef8a66-455c-4a01-bffd-3cae152e3730';

    //    // Retrieve all recipes for each meal type
    //     $breakfastRecipes = Recipe::where('meal_type_id', $breakfastMealTypeId)->get();
    //     $lunchRecipes = Recipe::where('meal_type_id', $lunchMealTypeId)->get();
    //     $snackRecipes = Recipe::where('meal_type_id', $snackMealTypeId)->get();
    //     $dinnerRecipes = Recipe::where('meal_type_id', $dinnerMealTypeId)->get();

    //     // Generate all possible combinations
    //     $combinations = [];
    //     foreach ($breakfastRecipes as $breakfastRecipe) {
    //         foreach ($lunchRecipes as $lunchRecipe) {
    //             foreach ($snackRecipes as $snackRecipe) {
    //                 foreach ($dinnerRecipes as $dinnerRecipe) {
    //                     $mealPlan= [
    //                         'breakfast' => $breakfastRecipe->id,
    //                         'lunch' => $lunchRecipe->id,
    //                         'snack' => $snackRecipe->id,
    //                         'dinner' => $dinnerRecipe->id,
    //                         'calories' => $breakfastRecipe->calories + $lunchRecipe->calories + $snackRecipe->calories + $dinnerRecipe->calories,
    //                         'carbohydrates' => $breakfastRecipe->carbohydrates + $lunchRecipe->carbohydrates + $snackRecipe->carbohydrates + $dinnerRecipe->carbohydrates,
    //                         'protein' => $breakfastRecipe->protein + $lunchRecipe->protein + $snackRecipe->protein + $dinnerRecipe->protein,
    //                         'total_fat' => $breakfastRecipe->total_fat + $lunchRecipe->total_fat + $snackRecipe->total_fat + $dinnerRecipe->total_fat,
    //                         'saturated_fat' => $breakfastRecipe->saturated_fat + $lunchRecipe->saturated_fat + $snackRecipe->saturated_fat + $dinnerRecipe->saturated_fat,
    //                         'sodium' => $breakfastRecipe->sodium + $lunchRecipe->sodium + $snackRecipe->sodium + $dinnerRecipe->sodium,
    //                         'sugar' => $breakfastRecipe->sugar + $lunchRecipe->sugar + $snackRecipe->sugar + $dinnerRecipe->sugar,
    //                     ];
    //                     MealPlan::create($mealPlan);
    //                 }
    //             }
    //         }
    //     }

        return response()->json(['status'=>true]);



    }
}
