<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ToppingController;
use Illuminate\Http\Request;
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
