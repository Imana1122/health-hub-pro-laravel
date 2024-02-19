<?php

namespace App\Http\Controllers;

use App\Models\MealType;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\UserAllergen;
use App\Models\UserCuisine;
use Illuminate\Http\Request;

class RecipeRecommendationController extends Controller
{
    public function getRecipeRecommendations(Request $request, $meal_type_id){
        // Retrieve all UserAllergen records for the authenticated user
        $userAllergens = UserAllergen::where('user_id', auth()->user()->id)->get();

        // Extract allergen_id values from the collection
        $allergenIds = $userAllergens->pluck('allergen_id')->toArray();

        // Retrieve all UserAllergen records for the authenticated user
        $userCuisines = UserCuisine::where('user_id', auth()->user()->id)->get();

        // Extract allergen_id values from the collection
        $cuisineIds = $userCuisines->pluck('cuisine_id')->toArray();

        // Retrieve all UserAllergen records for the authenticated user
        $userHealthConditions = UserAllergen::where('user_id', auth()->user()->id)->get();

        // Extract allergen_id values from the collection
        $healthConditionIds = $userHealthConditions->pluck('health_condition_id')->toArray();

        $recipes = Recipe::with('images')->with('ingredient')->where('meal_type_id',$meal_type_id)->orderBy('title','ASC');
        if ($request->get('keyword') != '') {
            $recipes = $recipes->where('recipes.title', 'like', '%' . $request->input('keyword') . '%');
        }

        // Filter by healthConditions
        if ($request->get('category') != '') {
            $recipes = $recipes->whereHas('category', function ($query) use ($request) {
                $query->where('category_id', $request->input('category'));
            });
        }

        // Filter by meal_type
        if ($request->get('meal_type') != '') {
            $recipes = $recipes->where('meal_type_id', $request->input('meal_type'));
        }

        // Filter by cuisine
        if (!empty($cuisineIds)) {
            $recipes = $recipes->whereIn('cuisine_id', $cuisineIds);
        }

        // Filter by allergens
        if (!empty($allergenIds)) {
            $recipes = $recipes->whereHas('allergenRecipes', function ($query) use ($allergenIds) {
                $query->whereIn('allergen_id', $allergenIds);
            });
        }

        // Filter by healthConditions
        if (!empty($healthConditionIds)) {
            $recipes = $recipes->whereHas('healthConditionRecipes', function ($query) use ($healthConditionIds) {
                $query->whereIn('health_condition_id', $healthConditionIds);
            });
        }
        $recipes = $recipes->paginate(5);

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
