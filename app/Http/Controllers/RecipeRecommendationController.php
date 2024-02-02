<?php

namespace App\Http\Controllers;

use App\Models\MealType;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;

class RecipeRecommendationController extends Controller
{
    public function getRecipeRecommendations(Request $request){
        $recipes = Recipe::with('images')->orderBy('title','ASC')->get();

        return response()->json([
            'status' => true,
            'recipes' => $recipes
        ]);
    }

    public function getRecipeCategories(Request $request){
        $recipeCategories = RecipeCategory::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'recipeCategories' => $recipeCategories
        ]);
    }

    public function getMealTypes(Request $request)
    {
        $mealTypes = MealType::orderBy('name', 'ASC')
            ->withCount('recipes') // Count the related recipes
            ->get();

        return response()->json([
            'status' => true,
            'recipeMealTypes' => $mealTypes
        ]);
    }

}