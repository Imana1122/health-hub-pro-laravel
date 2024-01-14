<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use App\Models\Recipe;
use App\Models\MealType;
use App\Models\RecipeImage;
use App\Models\RecipeTag;
use App\Models\Tag;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;


class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $recipes = Recipe::with(['meal_type', 'cuisine', 'images']);

        if ($request->get('keyword')) {
            $recipes = $recipes->where('recipes.title', 'like', '%' . $request->get('keyword') . '%');
        }

        $recipes = $recipes->paginate(10);

        return view("admin.recipes.list", compact('recipes'));
    }

    public function create(){
        $mealTypes = MealType::orderBy('name','ASC')->get();
        $cuisines = Cuisine::orderBy('name','ASC')->get();

        return view("admin.recipes.create", compact('cuisines','mealTypes'));
    }

    public function store(Request $request){

        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:recipes',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'cuisine_id' => 'required|exists:cuisines,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'calories' => 'nullable|numeric|min:0',
            'total_fat' => 'nullable|numeric|min:0',
            'saturated_fat' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'status' => 'required|in:0,1',
        ];



        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){
            $recipe = Recipe::create(
                $request->only(
                    'title',
                    'slug',
                    'description',
                    'tags',
                    'cuisine_id',
                    'meal_type_id',
                    'calories',
                    'total_fat',
                    'saturated_fat',
                    'sodium',
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
                    $destinationPath = public_path().'/uploads/recipes/large/'.$imageName;
                    $image = ImageManager::gd()->read($sourcePath);
                    $image->scale(height:1400);
                    $image->save($destinationPath);


                    //Small Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destinationPath = public_path().'/uploads/recipes/small/'.$imageName;
                    $image = ImageManager::gd()->read($sourcePath);
                    $image->resize(300,300);
                    $image->save($destinationPath);

                }


            }

            $tagArray = $request->input('tags', []);
            if(!empty($tagArray)){
                foreach($request->tags as $tag){
                    $slug = Str::slug($tag);

                    $newTag = Tag::updateOrCreate([
                        'slug'=> $slug,
                    ],
                    [
                        'name'=> $tag,
                        'slug' => $slug
                    ]);

                    RecipeTag::updateOrCreate(
                        [
                            'recipe_id' => $recipeId,
                            'tag_id' => $newTag->id, // Use tag ID here
                        ],
                        [
                            'recipe_id' => $recipeId,
                            'tag_id' => $newTag->id, // Use tag ID here
                        ]
                    );

                }


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

        $recipe = Recipe::find($recipeId);

        if(empty($recipe)){
            session()->flash('error','Recipe not found');

            return redirect()->route('recipes.index');
        }

        $images = $recipe->images;

        $mealTypes = MealType::orderBy('name','ASC')->get();
        $cuisines = Cuisine::orderBy('name','ASC')->get();

        return view("admin.recipes.edit", compact('mealTypes','cuisines', 'recipe','images'));
    }

    public function update($recipeId, Request $request){
        $recipe = Recipe::find($recipeId);
        if(empty($recipe)){
            session()->flash('error','Recipe not found');


            return redirect()->route('recipes.index')->with('error','Recipe not found');
        }


        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:recipes,slug,'.$recipe->id.',id',
            'description' => 'nullable|string',
            'tags' => 'nullable|string',
            'cuisine_id' => 'required|exists:cuisines,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'calories' => 'nullable|numeric|min:0',
            'total_fat' => 'nullable|numeric|min:0',
            'saturated_fat' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'status' => 'required|in:0,1',
        ];



        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){
            $recipe->update(
                $request->only(
                    'title',
                    'slug',
                    'description',
                    'tags',
                    'cuisine_id',
                    'meal_type_id',
                    'calories',
                    'total_fat',
                    'saturated_fat',
                    'sodium',
                    'status'
                )
            );

            session()->flash('success','Recipe updated successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Recipe updated successfully.'
            ]);
        }else{
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
                $basePath = public_path('uploads/recipes/'); // adjust the path based on your folder structure

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
