<?php

use App\Http\Controllers\AuthController;
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

Route::group(['prefix'=> 'account'], function () {
    Route::group(['middleware'=> 'guest'], function () {
        Route::post('/process-register', [AuthController::class,'processRegister'])->name('account.processRegister');
        Route::post('/process-login', [AuthController::class,'authenticate'])->name('account.authenticate');

    });

    Route::group(['middleware'=> 'auth:sanctum'], function () {
        Route::post('/update-profile', [AuthController::class,'updateProfile'])->name('account.updateProfile');
        // Route::post('/update-address', [AuthController::class,'updateAddress'])->name('account.updateAddress');
        Route::get('/logout', [AuthController::class,'logout'])->name('account.logout');

        Route::get('/order-detail/{orderId}', [AuthController::class,'orderDetail'])->name('account.orderDetail');
        // Route::post('/remove-product-from-wishlist', [AuthController::class,'removeProductFromWishlist'])->name('account.removeProductFromWishlist');
        Route::post('/process-change-password', [AuthController::class, 'changePassword'])->name('account.changePassword');
    });

});
