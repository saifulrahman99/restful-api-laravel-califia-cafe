<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ToppingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/', [CategoryController::class, 'update'])->middleware('auth:sanctum');
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware('auth:sanctum');
});
Route::group(['prefix' => 'toppings'], function () {
    Route::get('/', [ToppingController::class, 'index']);
    Route::post('/', [ToppingController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/', [ToppingController::class, 'update'])->middleware('auth:sanctum');
    Route::patch('/{id}', [ToppingController::class, 'updateStock'])->middleware('auth:sanctum');
    Route::get('/{id}', [ToppingController::class, 'show']);
    Route::delete('/{id}', [ToppingController::class, 'destroy'])->middleware('auth:sanctum');
});
Route::group(['prefix' => 'discounts'], function () {
    Route::get('/', [DiscountController::class, 'index']);
    Route::post('/', [DiscountController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/', [DiscountController::class, 'update'])->middleware('auth:sanctum');
    Route::patch('/{id}', [DiscountController::class, 'updateStatus'])->middleware('auth:sanctum');
    Route::get('/{id}', [DiscountController::class, 'show']);
    Route::delete('/{id}', [DiscountController::class, 'destroy'])->middleware('auth:sanctum');
});
Route::group(['prefix' => 'menus'], function () {
    Route::get('/', [MenuController::class, 'index']);
    Route::post('/', [MenuController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/', [MenuController::class, 'update'])->middleware('auth:sanctum');
    Route::patch('/{id}', [MenuController::class, 'updateStock'])->middleware('auth:sanctum');
    Route::get('/{id}', [MenuController::class, 'show']);
    Route::delete('/{id}', [MenuController::class, 'destroy'])->middleware('auth:sanctum');
});

// Rute public (Tanpa Auth)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
// Rute dengan Auth Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
});
