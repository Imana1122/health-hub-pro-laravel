<?php

namespace App\Http\Controllers;

use App\Models\MealType;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;

class RecipeRecommendationController extends Controller
{
    public function getRecipeRecommendations(Request $request, $meal_type_id){
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
        if ($request->get('cuisine') != '') {
            $recipes = $recipes->where('cuisine_id', $request->input('cuisine'));
        }

        // Filter by allergens
        if ($request->get('allergen') != '') {
            $recipes = $recipes->whereHas('allergenRecipes', function ($query) use ($request) {
                $query->where('allergen_id', $request->input('allergen'));
            });
        }

        // Filter by healthConditions
        if ($request->get('health_condition') != '') {
            $recipes = $recipes->whereHas('healthConditionRecipes', function ($query) use ($request) {
                $query->where('health_condition_id', $request->input('health_condition'));
            });
        }
        $recipes = $recipes->paginate(10);

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
