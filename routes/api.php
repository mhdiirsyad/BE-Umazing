<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\BearerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::middleware(['auth:sanctum', 'admin'])->group(function() {
    Route::apiResource('/products', \App\Http\Controllers\ProductController::class);
    Route::apiResource('/categories', \App\Http\Controllers\CategoryController::class);
    Route::apiResource('/orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show', 'destroy']);
});