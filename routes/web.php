<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\CuisineController;
use App\Http\Controllers\admin\MealTypeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\ExerciseController;
use App\Http\Controllers\admin\RecipeController;
use App\Http\Controllers\admin\RecipeImageController;
use App\Http\Controllers\admin\AllergenController;
use App\Http\Controllers\admin\ContactController;
use App\Http\Controllers\admin\DieticianController;
use App\Http\Controllers\Admin\DieticianSalaryPaymentController;
use App\Http\Controllers\admin\HealthConditionController;
use App\Http\Controllers\admin\IngredientController;
use App\Http\Controllers\admin\RecipeCategoryController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\TermsAndConditionsController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\WeightPlanController;
use App\Http\Controllers\admin\WorkoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admin.login');
});

Route::get('/reset-password', [AuthController::class,'resetPasswordForm'])->name('account.reset-password');
Route::post('/process-reset-password', [AuthController::class, 'resetPassword'])->name('account.resetPassword');



Route::group(['prefix'=> 'admin'], function () {
    Route::group(['middleware'=> 'admin.guest'], function () {
        Route::get('/login', [AdminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate', [AdminLoginController::class,'authenticate'])->name('admin.authenticate');
        Route::get('/forgot-password', [AdminLoginController::class, 'showForgotPasswordForm'])->name('admin.showForgotPasswordForm');
        Route::post('/process-forgot-password', [AdminLoginController::class, 'sendcode'])->name('admin.sendcode');
        Route::get('/verify-code', [AdminLoginController::class, 'showVerificationCodeForm'])->name('admin.showVerificationCodeForm');
        Route::post('/process-verify-code', [AdminLoginController::class, 'verifyCode'])->name('admin.verifyCode');
        Route::get('/reset-password', [AdminLoginController::class, 'showResetPasswordForm'])->name('admin.showResetPasswordForm');
        Route::post('/process-reset-password', [AdminLoginController::class, 'resetPassword'])->name('admin.resetPassword');
    });

    Route::group(['middleware'=> 'admin.auth'], function () {
        Route::get('/dashboard', [HomeController::class,'index'])->name('admin.dashboard');
        Route::get('/logout', [HomeController::class,'logout'])->name('admin.logout');

        //Change Password routes
        Route::get('/change-password', [SettingController::class, 'showChangePasswordForm'])->name('admin.showChangePasswordForm');
        Route::post('/process-change-password', [SettingController::class, 'changePassword'])->name('admin.changePassword');

        //Meal Types Routes
        Route::get('/mealTypes', [MealTypeController::class,'index'])->name('mealTypes.index');
        Route::get('/mealTypes/create', [MealTypeController::class,'create'])->name('mealTypes.create');
        Route::post('/mealTypes/store', [MealTypeController::class,'store'])->name('mealTypes.store');
        Route::get('/mealTypes/{id}/edit', [MealTypeController::class,'edit'])->name('mealTypes.edit');
        Route::put('/mealTypes/{id}', [MealTypeController::class,'update'])->name('mealTypes.update');
        Route::delete('/mealTypes/{id}', [MealTypeController::class,'destroy'])->name('mealTypes.destroy');

        //Temp Images Create
        Route::post('/upload-temp-image', [TempImagesController::class,'create'])->name('temp-images.create');
        Route::delete('/delete-temp-image', [TempImagesController::class,'delete'])->name('temp-images.delete');

        //Allergen Routess
        Route::get('/allergens', [AllergenController::class,'index'])->name('allergens.index');
        Route::get('/allergens/create', [AllergenController::class,'create'])->name('allergens.create');
        Route::post('/allergens/store', [AllergenController::class,'store'])->name('allergens.store');
        Route::get('/allergens/{id}/edit', [AllergenController::class,'edit'])->name('allergens.edit');
        Route::put('/allergens/{id}', [AllergenController::class,'update'])->name('allergens.update');
        Route::delete('/allergens/{id}', [AllergenController::class,'destroy'])->name('allergens.destroy');

        //Ingredient Routess
        Route::get('/ingredients', [IngredientController::class,'index'])->name('ingredients.index');
        Route::post('/ingredients/{id}', [IngredientController::class,'update'])->name('ingredients.update');
        Route::delete('/ingredients/{id}', [IngredientController::class,'destroy'])->name('ingredients.destroy');

        //Recipe Category Routess
        Route::get('/recipeCategories', [RecipeCategoryController::class,'index'])->name('recipeCategories.index');
        Route::get('/recipeCategories/create', [RecipeCategoryController::class,'create'])->name('recipeCategories.create');
        Route::post('/recipeCategories/store', [RecipeCategoryController::class,'store'])->name('recipeCategories.store');
        Route::get('/recipeCategories/{id}/edit', [RecipeCategoryController::class,'edit'])->name('recipeCategories.edit');
        Route::put('/recipeCategories/{id}', [RecipeCategoryController::class,'update'])->name('recipeCategories.update');
        Route::delete('/recipeCategories/{id}', [RecipeCategoryController::class,'destroy'])->name('recipeCategories.destroy');


        //HealthCondition Routess
        Route::get('/healthConditions', [HealthConditionController::class,'index'])->name('healthConditions.index');
        Route::get('/healthConditions/create', [HealthConditionController::class,'create'])->name('healthConditions.create');
        Route::post('/healthConditions/store', [HealthConditionController::class,'store'])->name('healthConditions.store');
        Route::get('/healthConditions/{id}/edit', [HealthConditionController::class,'edit'])->name('healthConditions.edit');
        Route::put('/healthConditions/{id}', [HealthConditionController::class,'update'])->name('healthConditions.update');
        Route::delete('/healthConditions/{id}', [HealthConditionController::class,'destroy'])->name('healthConditions.destroy');

        //Cuisine Routes
        Route::get('/cuisines', [CuisineController::class,'index'])->name('cuisines.index');
        Route::get('/cuisines/create', [CuisineController::class,'create'])->name('cuisines.create');
        Route::post('/cuisines/store', [CuisineController::class,'store'])->name('cuisines.store');
        Route::get('/cuisines/{id}/edit', [CuisineController::class,'edit'])->name('cuisines.edit');
        Route::put('/cuisines/{id}', [CuisineController::class,'update'])->name('cuisines.update');
        Route::delete('/cuisines/{id}', [CuisineController::class,'destroy'])->name('cuisines.destroy');

        //Terms and Conditions Routes
        Route::get('/termsAndConditions', [TermsAndConditionsController::class,'index'])->name('termsAndConditions.index');
        Route::get('/termsAndConditions/create', [TermsAndConditionsController::class,'create'])->name('termsAndConditions.create');
        Route::post('/termsAndConditions/store', [TermsAndConditionsController::class,'store'])->name('termsAndConditions.store');
        Route::delete('/termsAndConditions/{id}', [TermsAndConditionsController::class,'destroy'])->name('termsAndConditions.destroy');

        //Product Routes
        Route::get('/recipes', [RecipeController::class,'index'])->name('recipes.index');
        Route::get('/recipes/create', [RecipeController::class,'create'])->name('recipes.create');
        Route::post('/recipes/store', [RecipeController::class,'store'])->name('recipes.store');
        Route::get('/recipes/{recipe}/edit', [RecipeController::class,'edit'])->name('recipes.edit');
        Route::put('/recipes/{recipe}', [RecipeController::class,'update'])->name('recipes.update');
        Route::delete('/recipes/{recipe}', [RecipeController::class,'destroy'])->name('recipes.destroy');


        //User routes
        Route::get('/users', [UserController::class,'index'])->name('users.index');
        Route::get('/users/create', [UserController::class,'create'])->name('users.create');
        Route::post('/users/store', [UserController::class,'store'])->name('users.store');
        Route::get('/users/{id}/edit', [UserController::class,'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class,'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class,'destroy'])->name('users.destroy');

        //Admin routes
        Route::get('/admins', [AdminController::class,'index'])->name('admins.index');
        Route::get('/admins/create', [AdminController::class,'create'])->name('admins.create');
        Route::post('/admins/store', [AdminController::class,'store'])->name('admins.store');
        Route::get('/admins/{id}/edit', [AdminController::class,'edit'])->name('admins.edit');
        Route::put('/admins/{id}', [AdminController::class,'update'])->name('admins.update');
        Route::delete('/admins/{id}', [AdminController::class,'destroy'])->name('admins.destroy');


        //Change Password routes
        Route::get('/change-password', [SettingController::class, 'showChangePasswordForm'])->name('admin.showChangePasswordForm');
        Route::post('/process-change-password', [SettingController::class, 'changePassword'])->name('admin.changePassword');
        //profile
        Route::get('/profile', [SettingController::class,'profile'])->name('admin.profile');
        Route::post('/update-profile', [SettingController::class,'updateProfile'])->name('admin.updateProfile');
        Route::delete('/deleteAccount', [SettingController::class,'deleteAccount'])->name('admin.deleteAccount');

        //Temp Images Create
        Route::post('/recipe-images/update', [RecipeImageController::class,'update'])->name('recipe-images.update');
        Route::delete('/recipe-images/delete', [RecipeImageController::class,'delete'])->name('recipe-images.delete');

        //Exercises Routes
        Route::get('/exercises', [ExerciseController::class,'index'])->name('exercises.index');
        Route::get('/exercises/create', [ExerciseController::class,'create'])->name('exercises.create');
        Route::post('/exercises/store', [ExerciseController::class,'store'])->name('exercises.store');
        Route::get('/exercises/{id}/edit', [ExerciseController::class,'edit'])->name('exercises.edit');
        Route::put('/exercises/{id}', [ExerciseController::class,'update'])->name('exercises.update');
        Route::delete('/exercises/{id}', [ExerciseController::class,'destroy'])->name('exercises.destroy');

        //Workouts Routes
        Route::get('/workouts', [WorkoutController::class,'index'])->name('workouts.index');
        Route::get('/workouts/create', [WorkoutController::class,'create'])->name('workouts.create');
        Route::post('/workouts/store', [WorkoutController::class,'store'])->name('workouts.store');
        Route::get('/workouts/{id}/edit', [WorkoutController::class,'edit'])->name('workouts.edit');
        Route::put('/workouts/{id}', [WorkoutController::class,'update'])->name('workouts.update');
        Route::delete('/workouts/{id}', [WorkoutController::class,'destroy'])->name('workouts.destroy');


        //Contact Routes
        Route::get('/contact', [ContactController::class,'edit'])->name('contact.index');
        Route::put('/update-contact', [ContactController::class,'update'])->name('contact.update');


        //Weight Plan Routes
        Route::get('/weightPlans', [WeightPlanController::class,'index'])->name('weightPlans.index');
        Route::get('/weightPlans/create', [WeightPlanController::class,'create'])->name('weightPlans.create');
        Route::post('/weightPlans/store', [WeightPlanController::class,'store'])->name('weightPlans.store');
        Route::get('/weightPlans/{id}/edit', [WeightPlanController::class,'edit'])->name('weightPlans.edit');
        Route::put('/weightPlans/{id}', [WeightPlanController::class,'update'])->name('weightPlans.update');
        Route::delete('/weightPlans/{id}', [WeightPlanController::class,'destroy'])->name('weightPlans.destroy');

        //Meal Types Routes
        Route::get('/dieticians', [DieticianController::class,'index'])->name('dieticians.index');
        Route::get('/unapproveddieticians', [DieticianController::class,'getUnApprovedDieticians'])->name('unapproveddieticians.index');
        Route::delete('/dieticians/{id}', [DieticianController::class,'destroy'])->name('dieticians.destroy');
        Route::get('/dieticians/payment-details/{id}', [DieticianSalaryPaymentController::class,'index'])->name('dieticians.payment-details');
        Route::get('/dieticians/detail/{id}', [DieticianController::class,'detail'])->name('dieticians.detail');
        Route::put('/dieticians/approve-status/{id}', [DieticianController::class,'approveStatus'])->name('dieticians.approveStatus');
        Route::put('/dieticians/make-payment/{id}', [DieticianSalaryPaymentController::class,'makePayment'])->name('dieticians.payment');




        Route::get('/getSlug', function (Request $request) {
            $slug = '';
            if(!empty($request->title)){
                $slug = Str::slug($request->title);
            }
            return response()->json([
                'status'=>true,
                'slug'=>$slug
            ]);
        })->name('getSlug');
    });



});
