<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use App\Models\Cuisine;
use App\Models\Dietician;
use App\Models\Exercise;
use App\Models\HealthCondition;
use App\Models\Ingredient;
use App\Models\MealType;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeImage;
use App\Models\TempImage;
use App\Models\User;
use App\Models\WeightPlan;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function deleteUnusedIngredientImages() {
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

    public function deleteUnusedRecipeCategoryImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/recipeCategory/'));

        // Get all recipeCategories
        $recipeCategories = RecipeCategory::all();

        // Extract image names from the recipeCategories
        $recipeCategoryImages = $recipeCategories->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $recipeCategoryImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/recipeCategory/') . $unusedImage;
            $thumbPath = public_path('/uploads/recipeCategory/thumb/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }

    public function deleteUnusedRecipeImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/recipes/small/'));

        // Get all recipeCategories
        $recipeCategories = RecipeImage::all();

        // Extract image names from the recipeCategories
        $recipeImages = $recipeCategories->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $recipeImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/recipes/large/') . $unusedImage;
            $thumbPath = public_path('/uploads/recipes/small/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }

    public function deleteUnusedExerciseImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/exercise/'));

        // Get all exercises
        $exercises = Exercise::all();

        // Extract image names from the exercises
        $exerciseImages = $exercises->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $exerciseImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/exercise/') . $unusedImage;
            $thumbPath = public_path('/uploads/exercise/thumb/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }

    public function deleteUnusedWorkoutImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/workout/'));

        // Get all workouts
        $workouts = Workout::all();

        // Extract image names from the workouts
        $workoutImages = $workouts->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $workoutImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/workout/') . $unusedImage;
            $thumbPath = public_path('/uploads/workout/thumb/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }

    public function deleteUnusedWeightPlanImages() {
        // Get all images in the folder
        $allImages = File::files(public_path('/uploads/weightPlan/'));

        // Get all weightPlans
        $weightPlans = WeightPlan::all();

        // Extract image names from the weightPlans
        $weightPlanImages = $weightPlans->pluck('image')->toArray();

        // Identify images in the folder that are not in the database
        $unusedImages = array_diff(
            array_map('basename', $allImages),
            $weightPlanImages
        );

        // Delete unused images
        foreach ($unusedImages as $unusedImage) {
            $imagePath = public_path('/uploads/weightPlan/') . $unusedImage;
            $thumbPath = public_path('/uploads/weightPlan/thumb/') . $unusedImage;

            // Delete the image and its thumbnail from the folder
            File::delete($imagePath, $thumbPath);
        }
    }

    public function index()
    {
        $this->deleteUnusedIngredientImages();
        $this->deleteUnusedMealTypeImages();
        $this->deleteUnusedRecipeCategoryImages();
        $this->deleteUnusedRecipeImages();
        $this->deleteUnusedExerciseImages();
        $this->deleteUnusedWorkoutImages();
        $this->deleteUnusedWeightPlanImages();


        $recipesCount = Recipe::count();
        $usersCount = User::count();
        $dieticiansCount = Dietician::count();
        $ingredientsCount = Ingredient::count();
        $mealTypesCount = MealType::count();
        $cuisinesCount = Cuisine::count();
        $allergensCount = Allergen::count();
        $healthConditionsCount = HealthCondition::count();
        $exercisesCount = Exercise::count();
        $weightPlansCount = WeightPlan::count();
        $workoutsCount = Workout::count();
        $recipeCategoriesCount = RecipeCategory::count();


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



        return view("admin.dashboard",compact("usersCount","recipesCount","dieticiansCount","workoutsCount","weightPlansCount","exercisesCount","healthConditionsCount","allergensCount","cuisinesCount","mealTypesCount","ingredientsCount","recipeCategoriesCount"));
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
