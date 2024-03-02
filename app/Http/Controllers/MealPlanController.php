<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;

class MealPlanController extends Controller
{
    public function index(){
        $recipes = Recipe::first();
        // $recipes = $recipes->groupBy('meal_type_id');

        return response()->json($recipes);

    }
}
