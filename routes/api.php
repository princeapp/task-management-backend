<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AccountController::class, 'register']);
Route::post('/login',[AccountController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/profile', [AccountController::class, 'profile']);
    Route::post('/logout', [AccountController::class, 'logout']);
    

    Route::group(['prefix' => 'categories'], function() {
        Route::get('/', [CategoryController::class, 'getAll']);
        Route::post('/', [CategoryController::class, 'createNew']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'remove']);
    });

    Route::group(['prefix' => 'task'], function() {
        Route::get('/', [TaskController::class, 'getAll']);
        Route::post('/', [TaskController::class, 'createNew']);
        Route::put('/{id}', [TaskController::class, 'update']);
        Route::delete('/{id}', [TaskController::class, 'remove']);
        Route::post('/filter', [TaskController::class, 'filterByDate']);
    });
});