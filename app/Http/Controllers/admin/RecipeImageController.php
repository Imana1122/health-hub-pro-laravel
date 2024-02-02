<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RecipeImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;

class RecipeImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $recipeId = $request->recipe_id;

        $recipeImage = new RecipeImage();
        $recipeImage->recipe_id = $recipeId;
        $recipeImage->image = 'NULL';
        $recipeImage->save();

        $imageName = $recipeId.'-'.$recipeImage->id.'-'.time().'.'.$ext;
        $recipeImage->image = $imageName;
        $recipeImage->save();


        //Generate Recipe Thumbnail
        //Large Image
        $destinationPath = public_path().'/uploads/recipes/large/'.$imageName;
        // Create an instance of the ImageManager

        // Load the image
        $image = ImageManager::gd()->read($sourcePath);
        // scale the image
        $image->scale(height:1400);
        $image->save($destinationPath);

        //Small Image
        $destinationPath = public_path().'/uploads/recipes/small/'.$imageName;
        $image = ImageManager::gd()->read($sourcePath);
        $image->resize(300,300);
        $image->save($destinationPath);

        return response()->json([
            'status' => true,
            'image_id' => $recipeImage->id,
            'imagePath' => asset('uploads/recipes/small/'.$recipeImage->image),
            'message' => 'Image saved successfully'
        ]);

    }

    public function delete(Request $request)
    {
        $imageId = $request->id;
        // Find the image in the database
        $image = RecipeImage::findOrFail($imageId);
        if(empty($image)){
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ]);
        }
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

        // Delete the image record from the database
        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully',
        ]);

    }
}
