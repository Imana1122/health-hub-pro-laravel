<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\admin\CuisineController;
use App\Http\Controllers\admin\MealTypeController;
use App\Http\Controllers\admin\HomeController;
use App\Http\Controllers\admin\RecipeController;
use App\Http\Controllers\admin\RecipeImageController;
use App\Http\Controllers\admin\RecipeAllergenController;
use App\Http\Controllers\admin\AllergenController;
use App\Http\Controllers\admin\RecipeStepController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\UserController;
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

        //Category Routes
        Route::get('/mealTypes', [MealTypeController::class,'index'])->name('mealTypes.index');
        Route::get('/mealTypes/create', [MealTypeController::class,'create'])->name('mealTypes.create');
        Route::post('/mealTypes/store', [MealTypeController::class,'store'])->name('mealTypes.store');
        Route::get('/mealTypes/{category}/edit', [MealTypeController::class,'edit'])->name('mealTypes.edit');
        Route::put('/mealTypes/{category}', [MealTypeController::class,'update'])->name('mealTypes.update');
        Route::delete('/mealTypes/{category}', [MealTypeController::class,'destroy'])->name('mealTypes.destroy');

        //Temp Images Create
        Route::post('/upload-temp-image', [TempImagesController::class,'create'])->name('temp-images.create');
        Route::delete('/delete-temp-image', [TempImagesController::class,'delete'])->name('temp-images.delete');

        //Allergen Routess
        Route::get('/allergens', [AllergenController::class,'index'])->name('allergens.index');
        Route::get('/allergens/create', [AllergenController::class,'create'])->name('allergens.create');
        Route::post('/allergens/store', [AllergenController::class,'store'])->name('allergens.store');
        Route::get('/allergens/{subCategory}/edit', [AllergenController::class,'edit'])->name('allergens.edit');
        Route::put('/allergens/{subCategory}', [AllergenController::class,'update'])->name('allergens.update');
        Route::delete('/allergens/{subCategory}', [AllergenController::class,'destroy'])->name('allergens.destroy');

        //Cuisine Routes
        Route::get('/cuisines', [CuisineController::class,'index'])->name('cuisines.index');
        Route::get('/cuisines/create', [CuisineController::class,'create'])->name('cuisines.create');
        Route::post('/cuisines/store', [CuisineController::class,'store'])->name('cuisines.store');
        Route::get('/cuisines/{brand}/edit', [CuisineController::class,'edit'])->name('cuisines.edit');
        Route::put('/cuisines/{brand}', [CuisineController::class,'update'])->name('cuisines.update');
        Route::delete('/cuisines/{brand}', [CuisineController::class,'destroy'])->name('cuisines.destroy');

        //Product Routes
        Route::get('/recipes', [RecipeController::class,'index'])->name('recipes.index');
        Route::get('/recipes/create', [RecipeController::class,'create'])->name('recipes.create');
        Route::post('/recipes/store', [RecipeController::class,'store'])->name('recipes.store');
        Route::get('/recipes/{recipe}/edit', [RecipeController::class,'edit'])->name('recipes.edit');
        Route::put('/recipes/{recipe}', [RecipeController::class,'update'])->name('recipes.update');
        Route::delete('/recipes/{recipe}', [RecipeController::class,'destroy'])->name('recipes.destroy');
        Route::get('/recipe-subcategories', [RecipeAllergenController::class,'index'])->name('recipe-subcategories.index');

        //Recipe step Routes
        // Route::get('/recipe-steps', [RecipeStepController::class,'index'])->name('recipe-steps.index');
        Route::get('/recipe-steps/create', [RecipeStepController::class,'create'])->name('recipe-steps.create');
        // Route::post('/recipe-steps/store', [RecipeStepController::class,'store'])->name('recipe-steps.store');
        // Route::get('/recipe-steps/{step}/edit', [RecipeStepController::class,'edit'])->name('recipe-steps.edit');
        // Route::put('/recipe-steps/{step}', [RecipeStepController::class,'update'])->name('recipe-steps.update');
        // Route::delete('/recipe-steps/{step}', [RecipeStepController::class,'destroy'])->name('recipe-steps.destroy');

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
