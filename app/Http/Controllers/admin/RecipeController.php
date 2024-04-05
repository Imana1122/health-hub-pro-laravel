<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\AllergenRecipe;
use App\Models\Cuisine;
use App\Models\HealthCondition;
use App\Models\HealthConditionRecipe;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\MealType;
use App\Models\RecipeCategory;
use App\Models\RecipeImage;
use App\Models\RecipeIngredient;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;


class RecipeController extends Controller
{

    public function index(Request $request)
    {
        $recipes = Recipe::with(['meal_type', 'cuisine', 'images'])->orderBy('title', 'ASC');
        $allergens = Allergen::orderBy('name', 'ASC')->get();
        $mealTypes = MealType::orderBy('name', 'ASC')->get();
        $cuisines = Cuisine::orderBy('name', 'ASC')->get();
        $healthConditions = HealthCondition::orderBy('name', 'ASC')->get();
        $categories = RecipeCategory::orderBy('name', 'ASC')->get();

        if ($request->get('keyword') != '') {
            $recipes = $recipes->where('recipes.title', 'like', '%' . $request->input('keyword') . '%')->orWhere('recipes.id', 'like', '%' . $request->input('keyword') . '%');
        }

        // $categoryId = RecipeCategory::where('slug', 'smoothie')->first()->id;

        // $recipes->where('title', 'like', '%smoothie%')->each(function ($recipe) use ($categoryId) {
        //     $recipe->category_id = $categoryId;
        //     $recipe->save();
        // });


        // Filter by category
        if ($request->get('category') != '') {
            $recipes = $recipes->where('category_id', $request->input('category'));
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

        // $recipes = $recipes->doesntHave('images')->paginate(10);

        return view("admin.recipes.list", compact('recipes', 'allergens', 'mealTypes', 'cuisines', 'healthConditions','categories'));
    }


    public function create(){
        $mealTypes = MealType::orderBy('name','ASC')->get();
        $cuisines = Cuisine::orderBy('name','ASC')->get();
        $allergens = Allergen::orderBy('name','ASC')->get();
        $healthConditions = HealthCondition::orderBy('name','ASC')->get();
        $ingredients = Ingredient::orderBy('name','ASC')->get();

        return view("admin.recipes.create", compact('cuisines','mealTypes','allergens','healthConditions','ingredients'));
    }

    public function store(Request $request){
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:recipes',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'steps'=>'required|array',
            'ingredients' => 'required|array',
            'cuisine_id' => 'required|exists:cuisines,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'calories' => 'required|numeric|min:0',
            'total_fat' => 'required|numeric|min:0',
            'saturated_fat' => 'required|numeric|min:0',
            'sugar' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
        ];



        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){
            $recipe = Recipe::create(
                $request->only(
                    'title',
                    'slug',
                    'minutes',
                    'description',
                    'tags',
                    'steps',
                    'cuisine_id',
                    'meal_type_id',
                    'calories',
                    'carbohydrates',
                    'protein',
                    'total_fat',
                    'saturated_fat',
                    'sodium',
                    'sugar',
                    'status'
                )
            );
            $recipeId = $recipe->id;


            //Save Gallery Pics
            $imageArray = $request->input('image_array', []);

            if(!empty($imageArray)){
                foreach ($request->image_array as $temp_image_id){

                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray); //like jpg, gif, png, jpeg

                    $recipeImage = new RecipeImage();
                    $recipeImage->recipe_id = $recipeId;
                    $imageName = $recipeId.'-'.time().'.'.$ext;
                    $recipeImage->image = $imageName;
                    $recipeImage->save();

                    //Generate Recipe Thumbnail
                    //Large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destinationPath = public_path().'/storage/uploads/recipes/large/'.$imageName;
                    $image = ImageManager::gd()->read($sourcePath);
                    $image->scale(height:1400);
                    $image->save($destinationPath);


                    //Small Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destinationPath = public_path().'/storage/uploads/recipes/small/'.$imageName;
                    $image = ImageManager::gd()->read($sourcePath);
                    $image->resize(300,300);
                    $image->save($destinationPath);

                }


            }
            if(!empty($request->allergens)){
                foreach($request->allergens as $allergen){
                    AllergenRecipe::create([
                        'recipe_id' => $recipeId,
                        'allergen_id' => $allergen
                    ]);
                }
            }

            if(!empty($request->healthConditions)){
                foreach($request->healthConditions as $healthCondition){
                    HealthConditionRecipe::create([
                        'recipe_id' => $recipeId,
                        'health_condition_id' => $healthCondition
                    ]);
                }
            }

            foreach($request->ingredients as $ingredient){

                RecipeIngredient::updateOrCreate(
                    [
                        'ingredient_id' => $ingredient,
                        'recipe_id' => $recipe->id
                    ],
                    [
                        // Add additional fields if needed
                    ]
                );
            }

            session()->flash('success','Recipe created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Recipe created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


    }


    public function edit($recipeId, Request $request){

        $recipe = Recipe::with('allergenRecipes')->with('healthConditionRecipes')->with('recipeIngredients')->find($recipeId);
        $ingredients = Ingredient::orderBy('name','ASC')->get();
        $totalIngredients = $ingredients->count();
        // dd($totalIngredients);
        if(empty($recipe)){
            session()->flash('error','Recipe not found');

            return redirect()->route('recipes.index');
        }

        $images = $recipe->images;

        $mealTypes = MealType::orderBy('name','ASC')->get();
        $cuisines = Cuisine::orderBy('name','ASC')->get();
        $allergens = Allergen::orderBy('name','ASC')->get();
        $healthConditions = HealthCondition::orderBy('name','ASC')->get();
        $categories = RecipeCategory::orderBy('name','ASC')->get();

        return view("admin.recipes.edit", compact('mealTypes','categories','ingredients','cuisines', 'recipe','images','allergens','healthConditions'));
    }

    public function update($recipeId, Request $request)
    {
        $recipe = Recipe::find($recipeId);

        if (empty($recipe)) {
            session()->flash('error', 'Recipe not found');
            return redirect()->route('recipes.index')->with('error', 'Recipe not found');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:recipes,slug,' . $recipe->id . ',id',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'steps' => 'required|array',
            'ingredients' => 'required|array',
            'cuisine_id' => 'required|exists:cuisines,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'calories' => 'nullable|numeric|min:0',
            'total_fat' => 'nullable|numeric|min:0',
            'saturated_fat' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'status' => 'required|in:0,1',
            'sugar' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            $recipe->update($request->only(
                'title',
                'slug',
                'minutes',
                'description',
                'tags',
                'steps',
                'cuisine_id',
                'meal_type_id',
                'calories',
                'carbohydrates',
                'protein',
                'total_fat',
                'saturated_fat',
                'sodium',
                'sugar',
                'status'
            ));
            foreach($request->ingredients as $ingredient){

                RecipeIngredient::updateOrCreate(
                    [
                        'ingredient_id' => $ingredient,
                        'recipe_id' => $recipe->id
                    ],
                    [
                        // Add additional fields if needed
                    ]
                );

            }

            // Delete existing relationships
            $recipe->allergenRecipes()->delete();
            $recipe->healthConditionRecipes()->delete();

            // Create new relationships
            if (!empty($request->allergens)) {

                $recipe->allergenRecipes()->createMany(
                    collect($request->allergens)->map(function ($allergen) use ($request) {
                        return ['allergen_id' => $allergen, 'status' => $request->input('status', 1)];
                    })->all()
                );

            }

            if (!empty($request->healthConditions)) {
                $recipe->healthConditionRecipes()->createMany(
                    collect($request->healthConditions)->map(function ($healthCondition) use ($request) {
                        return ['health_condition_id' => $healthCondition, 'status' => $request->input('status', 1)];
                    })->all()
                );

            }

            session()->flash('success', 'Recipe updated successfully');

            return response()->json([
                'status' => true,
                'message' => 'Recipe updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($recipeId, Request $request){
        $recipe = Recipe::find($recipeId);

        if(empty($recipe)){
            session()->flash('error','Recipe not found');
            return response()->json([
                'status'=> false,
                'notFound'=> true,
                'error'=> 'Recipe not found'
            ]);
        }

        $images = $recipe->images;

        if (!empty($images)){
            foreach ($images as $image){
                $filename = $image->image;
                $basePath = public_path('storage/uploads/recipes/'); // adjust the path based on your folder structure

                // Delete the large image
                $imagePath = $basePath.'large/'.$filename;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }

                // Delete the small image
                $smallPath = $basePath . 'small/' . $filename;
                if (file_exists($smallPath)) {
                    unlink($smallPath);
                }
            }
            $recipe->images()->delete();
        }

        $recipe->delete();
        session()->flash('success','Recipe deleted successfully');
        return response()->json([
            'status'=> true,
            'message'=> 'Recipe Deleted Successfully'
        ]);
    }
}
