<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\dietician\DieticianAuthController;
use App\Http\Controllers\RecipeRecommendationController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\WorkoutRecommendationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('account')->group(function () {


    // // Routes accessible by guests
    Route::middleware('guest')->group(function () {
        Route::post('/process-register', [AuthController::class, 'processRegister'])->name('account.processRegister');
        Route::post('/process-login', [AuthController::class, 'authenticate'])->name('account.authenticate');
        Route::post('/process-forgot-password', [AuthController::class, 'sendcode'])->name('account.sendcode');

    });

    // Routes accessible by authenticated customers with Passport token
    Route::middleware('auth:customer')->group(function () {
        Route::post('/update-info', [AuthController::class, 'updateInfo'])->name('account.updateInfo');
        Route::post('/complete-profile', [AuthController::class, 'completeProfile'])->name('account.completeProfile');

        Route::post('/logout', [AuthController::class, 'logout'])->name('account.logout');
        Route::post('/process-change-password', [AuthController::class, 'changePassword'])->name('account.changePassword');

        Route::get('/weight-plans', [UserProfileController::class, 'getWeightPlans'])->name('account.workoutplans');
        Route::get('/allergens', [UserProfileController::class, 'getAllergens'])->name('account.allergens');
        Route::get('/cuisines', [UserProfileController::class, 'getCuisines'])->name('account.cuisines');
        Route::get('/health-conditions', [UserProfileController::class, 'getHealthConditions'])->name('account.healthConditions');

        Route::post('/choose-goal', [UserProfileController::class, 'chooseGoal'])->name('account.chooseGoal');
        Route::post('/set-cuisine-preferences', [UserProfileController::class, 'setCuisines'])->name('account.setCuisines');
        Route::post('/set-allergens', [UserProfileController::class, 'setAllergens'])->name('account.setAllergens');
        Route::post('/set-health-conditions', [UserProfileController::class, 'setHealthConditions'])->name('account.setHealthConditions');

        //Recipe Recommendations
        Route::get('/recipe-categories', [RecipeRecommendationController::class, 'getRecipeCategories'])->name('account.getRecipeCategories');
        Route::get('/recipe-meal-types', [RecipeRecommendationController::class, 'getMealTypes'])->name('account.getMealTypes');

        Route::get('/recipe-recommendations', [RecipeRecommendationController::class, 'getRecipeRecommendations'])->name('account.getRecipeRecommendations');

        //Workout Recommendations
        Route::get('/workout-recommendations', [WorkoutRecommendationController::class, 'getWorkoutRecommendations'])->name('account.getWorkoutRecommendations');

    });

});

Route::prefix('dietician')->group(function () {


    // // Routes accessible by guests
    Route::middleware('guest')->group(function () {
        Route::post('/process-register', [DieticianAuthController::class, 'processRegister'])->name('dietician.processRegister');
        Route::post('/process-login', [DieticianAuthController::class, 'authenticate'])->name('dietician.authenticate');
        Route::post('/process-forgot-password', [DieticianAuthController::class, 'sendcode'])->name('dietician.sendcode');

    });

    // Routes accessible by authenticated customers with Passport token
    Route::middleware('auth:dietician')->group(function () {
        Route::post('/update-profile', [DieticianAuthController::class, 'updateProfile'])->name('dietician.updateProfile');
        Route::get('/logout', [DieticianAuthController::class, 'logout'])->name('dietician.logout');
        Route::post('/process-change-password', [DieticianAuthController::class, 'changePassword'])->name('dietician.changePassword');
    });

});
