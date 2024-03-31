<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\CustomizedWorkoutController;
use App\Http\Controllers\dietician\DieticianAuthController;
use App\Http\Controllers\Dietician\DieticianHomeController;
use App\Http\Controllers\DieticianRatingController;
use App\Http\Controllers\DieticianSubscriptionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RecipeRecommendationController;
use App\Http\Controllers\UserMealPlanController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserRecipeLogController;
use App\Http\Controllers\WorkoutLogController;
use App\Http\Controllers\WorkoutRecommendationController;
use App\Http\Controllers\WorkoutScheduleController;
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
Route::get('/', function () {
    // Authentication failed
    return response()->json(['status'=>false,'message'=>'Not authenticated','authenticated' => false]);
})->name('login');

// Route::get('/meal-plans', [MealPlanController::class, 'index'])->name('mealplans.index');

Route::prefix('account')->group(function () {
    // Routes accessible by guests
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

        //Home
        Route::get('/home-details/{now}', [HomeController::class, 'index'])->name('account.home');

        //Meal Plans
        Route::get('/meal-plans', [UserMealPlanController::class, 'index'])->name('account.getMealPlans');
        Route::post('/select-meal-plan', [UserMealPlanController::class, 'selectMealPlan'])->name('account.selectMealPlan');

        //Recipe Recommendations
        Route::get('/recipe-categories', [RecipeRecommendationController::class, 'getRecipeCategories'])->name('account.getRecipeCategories');
        Route::get('/recipe-meal-types', [RecipeRecommendationController::class, 'getMealTypes'])->name('account.getMealTypes');
        Route::get('/recipe-recommendations/{meal_type_id}', [RecipeRecommendationController::class, 'getRecipeRecommendations'])->name('account.getRecipeRecommendations');
        Route::get('/setTodayGoal', [UserMealPlanController::class, 'setTodayGoal'])->name('account.setTodayGoal');
        Route::get('/meal-plan-recommendations', [UserMealPlanController::class, 'index'])->name('account.mealPlans');
        //meal logs
        Route::post('/log-meal', [UserRecipeLogController::class, 'logMeal'])->name('account.logMeal');
        Route::get('/get-meal-logs/{now}', [UserRecipeLogController::class, 'getMealLogs'])->name('account.getMealLogs');
        Route::get('/get-linechart-details/{type}', [UserRecipeLogController::class, 'getLineGraphDetails'])->name('account.getLineGraphDetails');
        Route::delete('/deleteMealLog/{id}', [UserRecipeLogController::class, 'deleteMealLog'])->name('account.deleteMealLog');

        //Workout Recommendations
        Route::get('/workout-recommendations', [WorkoutRecommendationController::class, 'getWorkoutRecommendations'])->name('account.getWorkoutRecommendations');
        Route::get('workout-exercises/{id}', [WorkoutRecommendationController::class, 'getWorkoutwithExercise'])->name('account.getWorkoutExercises');

        //Dietician
        Route::get('/get-dieticians', [DieticianSubscriptionController::class, 'getDieticians'])->name('account.getDieticians');
        Route::get('/get-booked-dieticians', [DieticianSubscriptionController::class, 'getBookedDieticians'])->name('account.getBookedDieticians');
        Route::post('/book-dieticians', [DieticianSubscriptionController::class, 'bookDietician'])->name('account.bookDietician');
        Route::post('/verify-booking-payment', [DieticianSubscriptionController::class, 'verifyBookingPayment'])->name('account.verifyBookingPayment');
        Route::post('/save-rating/{id}', [DieticianRatingController::class, 'saveRating'])->name('account.dietician.saveRating');

        //Customize Workout
        Route::get('/get-customized-workouts', [CustomizedWorkoutController::class, 'getCustomizedWorkouts'])->name('account.getCustomizedWorkouts');
        Route::get('/get-exercises', [CustomizedWorkoutController::class, 'getExercises'])->name('account.getExercises');
        Route::post('/customized-workout/store', [CustomizedWorkoutController::class, 'store'])->name('account.customizedWorkout.store');
        Route::post('/schedule-workout', [WorkoutScheduleController::class, 'scheduleWorkout'])->name('account.scheduleWorkout');
        Route::get('/get-scheduled-workouts/{now}', [WorkoutScheduleController::class, 'getScheduledWorkouts'])->name('account.getScheduledWorkouts');
        Route::get('/get-upcoming-workouts', [WorkoutScheduleController::class, 'getUpcomingWorkouts'])->name('account.getUpcomingWorkouts');
        Route::put('/update-workout-notifiable', [WorkoutScheduleController::class, 'updateNotifiable'])->name('account.updateNotifiable');
        Route::put('/update-workout-done', [WorkoutScheduleController::class, 'updateDone'])->name('account.updateDone');

        //Workout
        Route::post('/log-workout', [WorkoutLogController::class, 'logWorkout'])->name('account.logWorkout');
        Route::get('/get-workout-logs/{now}', [WorkoutLogController::class, 'getWorkoutLogs'])->name('account.getWorkoutLogs');
        Route::get('/get-workout-linechart-details/{type}', [WorkoutLogController::class, 'getWorkoutLineGraphDetails'])->name('account.getWorkoutLineGraphDetails');
        Route::delete('/deleteWorkoutLog/{id}', [WorkoutLogController::class, 'deleteWorkoutLog'])->name('account.deleteWorkoutLog');

        //Chat
        Route::get('/chats/participants', [ChatMessageController::class, 'getChatDieticians'])->name('account.chats.dieticians');
        Route::get('/chats/{id}', [ChatMessageController::class, 'loadMoreMessages'])->name('account.chats.loadMore');
        Route::post('/chats/store', [ChatMessageController::class, 'storeByUser'])->name('account.chats.store');
        Route::post('/chats/read', [ChatMessageController::class, 'setChatMessagesRead'])->name('account.chats.read');

        //Notification Routes
        Route::get('/notifications', [NotificationController::class,'getNotifications'])->name('account.notifications.index');
        Route::put('/notifications/read', [NotificationController::class,'readNotifications'])->name('account.notifications.read');

        //Progress Routes
        Route::post('/progress/store', [ProgressController::class, 'store'])->name('account.progress.store');


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
        Route::post('/logout', [DieticianAuthController::class, 'logout'])->name('dietician.logout');
        Route::post('/process-change-password', [DieticianAuthController::class, 'changePassword'])->name('dietician.changePassword');

        Route::get('/chats/participants', [ChatMessageController::class, 'getChatUsers'])->name('dietician.chats.users');
        Route::get('/chats/{id}', [ChatMessageController::class, 'loadMoreMessages'])->name('dietician.chats.loadMore');
        Route::post('/chats/store', [ChatMessageController::class, 'storeByDietician'])->name('dietician.chats.store');
        Route::post('/chats/read', [ChatMessageController::class, 'setChatMessagesRead'])->name('dietician.chats.read');

        //Notification Routes
        Route::get('/notifications', [NotificationController::class,'getNotifications'])->name('dietician.notifications.index');
        Route::put('/notifications/read', [NotificationController::class,'readNotifications'])->name('dietician.notifications.read');

        //Home Routes
        Route::get('/home-details', [DieticianHomeController::class,'index'])->name('dietician.home.index');
        Route::get('/get-payment-details', [DieticianHomeController::class,'getPaymentDetails'])->name('dietician.payment-details');
        Route::get('/get-payments', [DieticianHomeController::class,'getPayments'])->name('dietician.payments');
        Route::get('/get-ratings', [DieticianHomeController::class,'getRatings'])->name('dietician.ratings');

    });

});
