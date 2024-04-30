<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use App\Models\AllergenRecipe;
use App\Models\HealthCondition;
use App\Models\HealthConditionRecipe;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use Illuminate\Http\Request;

class OtherController extends Controller
{

    public function setRecipeCategories(){

        // AllergenRecipe::truncate();
        // HealthConditionRecipe::truncate();

        $recipes = Recipe::whereHas('ingredient', function ($query) {
            $query->where('slug', 'like', '%rice%');
        });

        // $recipes = Recipe::whereDoesntHave('ingredient', function ($query) {
        //     $query->where('slug', 'ghee')
        //           ->orWhere('slug', 'unsalted-butter')
        //           ->orWhere('slug', 'buttermilk')
        //           ->orWhere('slug', 'like', '%beer%')
        //           ->orWhere('slug', 'butter')
        //           ->orWhere('slug', 'like', '%evaporated-milk%')
        //           ->orWhere('slug', 'like', '%cheese%')
        //           ->orWhere('slug', 'like', '%yogurt%')
        //           ->orWhere('slug', 'milk')
        //           ->orWhere('slug', 'like', '%sweetened-condensed-milk%')
        //           ->orWhere('slug', 'like', '%whole-milk%')
        //           ->orWhere('slug', 'like', '%powdered-milk%')
        //           ->orWhere('slug', 'like', '%skim-milk%')
        //           ->orWhere('slug', 'like', '%1-low-fat-milk%');
        // });


        // $recipes = Recipe::whereDoesntHave('ingredient', function ($query) {
        //     $query->where('slug', 'like', '%beer%')
        //     ->orWhere('slug', 'like', '%barley%')
        //     ->orWhere('slug', 'like', '%wheat%')
        //     ->orWhere('slug', 'like', '%frozen%')
        //     ->orWhere('slug', 'like', '%soy-sauce%')
        //     ->orWhere('slug', 'like', '%hoisin-sauce%')
        //     ->orWhere('slug', 'like', '%teriyaki%')
        //     ->orWhere('slug', 'like', '%dressing%')
        //     ->orWhere('slug', 'like', '%tortilla%')
        //     ->orWhere('slug', 'like', '%pasta%')
        //     ->orWhere('slug', 'like', '%noodle%')
        //     ->orWhere('slug', 'like', '%bake%')
        //     ->orWhere('slug', 'like', '%flour%');
        // });


        // $recipes = Recipe::whereDoesntHave('ingredient', function ($query) {
        //     $query->where('slug', 'like', '%beer%')
        //     ->orWhere('slug', 'like', '%white-rice%')
        //     ->orWhere('slug', 'like', '%white-bread%')
        //     ->orWhere('slug', 'like', '%sweet%')
        //     ->orWhere('slug', 'like', '%instant%')
        //     ->orWhere('slug', 'like', '%bake%')
        //     ->orWhere('slug', 'like', '%fried%')
        //     ->orWhere('slug', 'like', '%sugar%');
        // });

        // $recipes = $recipes->whereHas('ingredient', function ($query) {
        //     $query->where('slug', 'like', '%bell-pepper%')
        //     ->orWhere('slug', 'like', '%quinoa%')
        //     ->orWhere('slug', 'like', '%brown-rice%')
        //     ->orWhere('slug', 'like', '%fish%')
        //     ->orWhere('slug', 'like', '%tofu%')
        //     ->orWhere('slug', 'like', '%lentil%')
        //     ->orWhere('slug', 'like', '%bean%')
        //     ->orWhere('slug', 'like', '%barley%')
        //     ->orWhere('slug', 'like', '%lean%')
        //     ->orWhere('slug', 'like', '%broccoli%')
        //     ->orWhere('slug', 'like', '%green%')
        //     ->orWhere('slug', 'like', '%carrot%')
        //     ->orWhere('slug', 'like', '%spinach%')
        //     ->orWhere('slug', 'like', '%lettuce%')
        //     ->orWhere('slug', 'like', '%carrot%')
        //     ->orWhere('slug', 'like', '%salmon%')
        //     ->orWhere('slug', 'like', '%nut%')
        //     ->orWhere('slug', 'like', '%almond%')
        //     ->orWhere('slug', 'like', '%peanut%')
        //     ->orWhere('slug', 'like', '%cashew%')
        //     ->orWhere('slug', 'like', '%seed%')
        //     ->orWhere('slug', 'like', '%pistachio%')
        //     ->orWhere('slug', 'like', '%pecan%')
        //     ->orWhere('slug', 'like', '%leaf%')
        //     ->orWhere('slug', 'like', '%pea%')
        //     ->orWhere('slug', 'like', '%tuna%')
        //     ->orWhere('slug', 'like', '%green%')
        //     ->orWhere('slug', 'like', '%leaves%')

        //     ->orWhere('slug', 'like', '%carrot%');
        // });

        // $recipes = Recipe::whereDoesntHave('ingredient', function ($query) {
        //     $query->where('slug', 'like', '%butter%')
        //     ->orWhere('slug', 'like', '%lard%')
        //     ->orWhere('slug', 'like', '%margarine%')
        //     ->orWhere('slug', 'like', '%shortening%')
        //     ->orWhere('slug', 'like', '%cream%')
        //     ->orWhere('slug', 'like', '%cheese%')
        //     ->orWhere('slug', 'like', '%bacon%')
        //     ->orWhere('slug', 'like', '%sausage%')
        //     ->orWhere('slug', 'like', '%processed%')
        //     ->orWhere('slug', 'like', '%fatty%')
        //     ->orWhere('slug', 'like', '%fried%')
        //     ->orWhere('slug', 'like', '%instant%')
        //     ->orWhere('slug', 'like', '%trans%');
        // });

        // $recipes = $recipes->whereHas('ingredient', function ($query) {
        //     $query->where('slug', 'like', '%bell-pepper%')
        //     ->orWhere('slug', 'like', '%quinoa%')
        //     ->orWhere('slug', 'like', '%brown-rice%')
        //     ->orWhere('slug', 'like', '%fish%')
        //     ->orWhere('slug', 'like', '%tofu%')
        //     ->orWhere('slug', 'like', '%lentil%')
        //     ->orWhere('slug', 'like', '%bean%')
        //     ->orWhere('slug', 'like', '%barley%')
        //     ->orWhere('slug', 'like', '%lean%')
        //     ->orWhere('slug', 'like', '%broccoli%')
        //     ->orWhere('slug', 'like', '%green%')
        //     ->orWhere('slug', 'like', '%carrot%')
        //     ->orWhere('slug', 'like', '%spinach%')
        //     ->orWhere('slug', 'like', '%lettuce%')
        //     ->orWhere('slug', 'like', '%carrot%')
        //     ->orWhere('slug', 'like', '%salmon%')
        //     ->orWhere('slug', 'like', '%nut%')
        //     ->orWhere('slug', 'like', '%almond%')
        //     ->orWhere('slug', 'like', '%peanut%')
        //     ->orWhere('slug', 'like', '%cashew%')
        //     ->orWhere('slug', 'like', '%seed%')
        //     ->orWhere('slug', 'like', '%pistachio%')
        //     ->orWhere('slug', 'like', '%pecan%')
        //     ->orWhere('slug', 'like', '%leaf%')
        //     ->orWhere('slug', 'like', '%pea%')
        //     ->orWhere('slug', 'like', '%tuna%')
        //     ->orWhere('slug', 'like', '%green%')
        //     ->orWhere('slug', 'like', '%leaves%')

        //     ->orWhere('slug', 'like', '%carrot%');
        // });


        $recipes=$recipes->get();

        // return response()->json(count($recipes));

        foreach ($recipes as $recipe) {

            $category = RecipeCategory::where('slug', 'rice')->first();
            $recipe=Recipe::find($recipe->id);
            if ($category) {
                $recipe->category_id = $category->id;
                $recipe->save();
            }

        }

        // foreach ($recipes as $recipe) {
        //     $allergen = Allergen::where('slug', 'diary')->first();
        //     $recipe=Recipe::find($recipe->id);
        //     if ($allergen) {
        //         $allergenRecipe = AllergenRecipe::updateOrCreate([
        //             'allergen_id'=>$allergen->id,
        //             'recipe_id'=>$recipe->id
        //         ],
        //         [
        //             'allergen_id'=>$allergen->id,
        //             'recipe_id'=>$recipe->id
        //         ]);

        //     }

        // }

        foreach ($recipes as $recipe) {


            $healthCondition = HealthCondition::where('slug', 'cholesterol')->first();
            $recipe=Recipe::find($recipe->id);
            if ($healthCondition) {
                $healthConditionRecipe = HealthConditionRecipe::updateOrCreate([
                    'health_condition_id'=>$healthCondition->id,
                    'recipe_id'=>$recipe->id
                ],
                [
                    'health_condition_id'=>$healthCondition->id,
                    'recipe_id'=>$recipe->id
                ]);

            }

        }


    }
}
