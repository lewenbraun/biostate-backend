<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\MealController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\StatisticsController;

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'user'], function () {
        Route::post('/update', [UserController::class, 'update']);
        Route::get('/max-nutrients', [UserController::class, 'maxNutrients']);
        Route::get('/profile-data', [UserController::class, 'profileData']);
    });

    Route::group(['prefix' => 'daily-meal'], function () {
        Route::get('/', [MealController::class, 'show']);
        Route::post('/product/add', [MealController::class, 'addProductIntoMeal']);
        Route::post('/product/delete', [MealController::class, 'deleteProduct']);
        Route::post('/product/increase-count', [MealController::class, 'increaseCountProduct']);
        Route::post('/product/decrease-count', [MealController::class, 'decreaseCountProduct']);
        Route::post('/product/update-weight', [MealController::class, 'updateWeightProduct']);
        Route::post('/meal/create', [MealController::class, 'createMeal']);
        Route::post('/meal/delete', [MealController::class, 'deleteMeal']);

        Route::group(['prefix' => 'statistics'], function () {
            Route::get('/day', [MealController::class, 'statisticsPerDay']);
        });
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::post('/create', [ProductController::class, 'create']);
        Route::get('/search/{name}', [ProductController::class, 'search']);
        Route::get('/show/{product}', [ProductController::class, 'show']);
        Route::post('/update', [ProductController::class, 'update']);
        Route::post('/delete', [ProductController::class, 'delete']);
    });

    Route::group(['prefix' => 'statistics'], function () {
        Route::get('/sum-nutrients-for-period-date', [StatisticsController::class, 'sumNutrientsForPeriodDate']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
