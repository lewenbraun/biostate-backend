<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\MealController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\StatisticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/user/update', [UserController::class, 'update']);
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
        Route::post('/', [ProductController::class, 'store']);
        Route::get('/search/{name}', [ProductController::class, 'search']);
        Route::get('/show/{product}', [ProductController::class, 'show']);
        Route::post('/update', [ProductController::class, 'update']);
        Route::post('/delete', [ProductController::class, 'delete']);
    });

    Route::group(['prefix' => 'statistics'], function () {
        Route::get('/sum-nutrients-for-period-date', [StatisticsController::class, 'sumNutrientsForPeriodDate']);
    });
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
