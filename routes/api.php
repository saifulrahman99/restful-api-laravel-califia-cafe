<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'categories'], function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/', [CategoryController::class, 'update']);
    Route::get('/{id}', [CategoryController::class, 'show']);
    Route::delete('/{id}', [CategoryController::class, 'destroy']);
});
