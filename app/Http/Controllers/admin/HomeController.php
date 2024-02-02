<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Dietician;
use App\Models\Ingredient;
use App\Models\MealType;
use App\Models\Recipe;
use App\Models\TempImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function deleteUnusedImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/ingredient/'));

        // Get all ingredients
        $ingredients = Ingredient::all();

        // Extract image names from the ingredients
        $ingredientImages = $ingredients->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $ingredientImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/ingredient/') . $unusedImage;
            $thumbPath = public_path('/uploads/ingredient/thumb/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }

    public function deleteUnusedMealTypeImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/mealType/'));

        // Get all mealTypes
        $mealTypes = MealType::all();

        // Extract image names from the mealTypes
        $mealTypeImages = $mealTypes->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $mealTypeImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/mealType/') . $unusedImage;
            $thumbPath = public_path('/uploads/mealType/thumb/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }


    public function index()
    {
        $this->deleteUnusedImages();


        $recipesCount = Recipe::count();
        $usersCount = User::count();
        $dieticiansCount = User::count();


        $currentDateTime = Carbon::now();

        $tempImages = TempImage::where('created_at','<=', $currentDateTime)->get();

        foreach( $tempImages as $image ){
            $path = public_path('/temp/'. $image->name);
            $thumbPath = public_path('/temp/thumb/'. $image->name);

            //Delete main image
            if( File::exists( $path ) ){
                File::delete( $path );
            }

            if( File::exists( $thumbPath ) ){
                File::delete( $thumbPath );
            }

            TempImage::where('id',$image->id)->delete();
        }



        return view("admin.dashboard",compact("usersCount","recipesCount","dieticiansCount"));
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
