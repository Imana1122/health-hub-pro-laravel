<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Illuminate\Support\Str;

class IngredientController extends Controller
{
    public function index(Request $request){
        // // Find the ingredient with the slug 'egg-whites'
        // $oldIngredient = Ingredient::where('slug', 'green-onion')->first();

        // // Find the ingredient with the slug 'egg-white'
        // $newIngredient = Ingredient::where('slug', 'green-onions')->first();

        // if ($oldIngredient && $newIngredient) {
        //     // Update the recipe_ingredients table
        //     RecipeIngredient::where('ingredient_id', $oldIngredient->id)
        //         ->update(['ingredient_id' => $newIngredient->id]);

        //     // You may also want to delete the old ingredient record if it's no longer needed
        //     $oldIngredient->delete();
        // } else {
        //     // Handle if either 'egg-whites' or 'egg-white' is not found
        //     echo('Not found');
        // }

        // $recipes = Recipe::orderBy('id', 'ASC')->get();

        // foreach ($recipes as $recipe) {
        //     $ingredients = $recipe->ingredients;

        //     foreach ($ingredients as $item) {
        //         $slug = Str::slug($item, '-'); // Use Str::slug to create a slug
        //         $ingredient = Ingredient::updateOrCreate(
        //             ['slug' => $slug],
        //             ['name' => $item]
        //         );

        //         RecipeIngredient::updateOrCreate(
        //             [
        //                 'ingredient_id' => $ingredient->id,
        //                 'recipe_id' => $recipe->id
        //             ],
        //             [
        //                 // Add additional fields if needed
        //             ]
        //         );
        //     }
        // }

        $ingredients = Ingredient::orderBy('name','ASC');
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $ingredients = $ingredients->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $ingredients = $ingredients->paginate(10);

        return view("admin.ingredient.list", compact('ingredients'));
    }


    public function update($ingredientId, Request $request){
        $ingredient = Ingredient::find($ingredientId);
        if(empty($ingredient)){
            session()->flash('error','Ingredient not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Ingredient not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "image" => "required|image",
        ]);

        if ($validator->passes()) {

            //Save Image Here
            if($request->image){
                $image = $request->image;
                $ext = $image->getClientOriginalExtension();
                $newName = $ingredient->id.'.'.$ext;

                $ingredient->image =$newName;
                $ingredient->save();

                $image->move(public_path().'/images',$newName);

                //Generate thumbnail
                $sourcePath = public_path().'/images/'.$newName; // Fix the path
                $destPath = public_path().'/storage/uploads/ingredient/'.$newName; // Fix the path

                $image = ImageManager::gd()->read($sourcePath);
                $image->resize(450, 600);
                $image->save($destPath);


                // Generate image thumbnail
                $dPathThumbnail = 'uploads/ingredient/thumb/' . $newName;
                $img = ImageManager::gd()->read($sourcePath);
                $img->resize(450, 600);
                $img->save(public_path('/storage/' . $dPathThumbnail)); // Save thumbnail to storage


            }

            session()->flash("success","Ingredient updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Ingredient updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($ingredientId, Request $request){
        $ingredient =  Ingredient::find($ingredientId);
        if(empty($ingredient)){
            session()->flash('error','Ingredient not found');
            return response()->json([
                'status' => false,
                'message' => 'Ingredient not found'
            ]);
        }

        // Check if ingredient has an existing image
        if (!empty($ingredient->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/storage/uploads/ingredient/' . $ingredient->image);
            $oldThumbnailPath = public_path('/storage/uploads/ingredient/thumb/' . $ingredient->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $ingredient->delete();

        session()->flash('success','Ingredient deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Ingredient deleted successfully'
        ]);

    }
}
