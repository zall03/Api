<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChildController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\StockController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/auth/google', [AuthController::class, 'googleLogin']);

Route::get('/ingredients/image/{file}', [IngredientController::class, 'image'])
    ->where('file', '[A-Za-z0-9._-]+');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateMe']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/categories', [IngredientController::class, 'categories']);
    Route::get('/ingredients', [IngredientController::class, 'index']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show']);

    Route::get('/stocks', [StockController::class, 'index']);
    Route::post('/stocks', [StockController::class, 'store']);
    Route::get('/stocks/{stock}', [StockController::class, 'show']);
    Route::put('/stocks/{stock}', [StockController::class, 'update']);
    Route::delete('/stocks/{stock}', [StockController::class, 'destroy']);

    Route::get('/children', [ChildController::class, 'index']);
    Route::post('/children', [ChildController::class, 'store']);
    Route::get('/children/{child}', [ChildController::class, 'show']);
    Route::put('/children/{child}', [ChildController::class, 'update']);
    Route::delete('/children/{child}', [ChildController::class, 'destroy']);

    Route::get('/children/{child}/measurements', [ChildController::class, 'measurements']);
    Route::post('/children/{child}/measurements', [ChildController::class, 'storeMeasurement']);

    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::post('/recipes/generate', [RecipeController::class, 'generate']);
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
});
