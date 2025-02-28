<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ToppingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});
Route::group(['prefix' => 'toppings'], function () {
    Route::get('/', [ToppingController::class, 'index']);
    Route::post('/', [ToppingController::class, 'store']);
    Route::put('/', [ToppingController::class, 'update']);
    Route::patch('/{id}', [ToppingController::class, 'updateStock']);
    Route::get('/{id}', [ToppingController::class, 'show']);
    Route::delete('/{id}', [ToppingController::class, 'destroy']);
});
Route::group(['prefix' => 'discounts'], function () {
    Route::get('/', [DiscountController::class, 'index']);
    Route::post('/', [DiscountController::class, 'store']);
    Route::put('/', [DiscountController::class, 'update']);
    Route::patch('/{id}', [DiscountController::class, 'updateStatus']);
    Route::get('/{id}', [DiscountController::class, 'show']);
    Route::delete('/{id}', [DiscountController::class, 'destroy']);
});
Route::group(['prefix' => 'menus'], function () {
    Route::get('/', [MenuController::class, 'index']);
    Route::post('/', [MenuController::class, 'store']);
    Route::put('/', [MenuController::class, 'update']);
    Route::patch('/{id}', [MenuController::class, 'updateStock']);
    Route::get('/{id}', [MenuController::class, 'show']);
    Route::delete('/{id}', [MenuController::class, 'destroy']);
});
