<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/category', [CategoryController::class, 'index']);
Route::get('/category/{id}', [CategoryController::class, 'show']);

Route::get('/product', [ProductController::class, 'index']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:user'])->group(function() {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/items/{id}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{id}', [CartController::class, 'removeItem']);

    // Order
    Route::post('/order', [OrderController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function() {
    // Category
    Route::post('/category', [CategoryController::class, 'store']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    // Product
    Route::post('/product', [ProductController::class, 'store']);
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);

    // Order
    Route::put('/order/id', [OrderController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'role:user,admin'])->group(function() {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/order/{id}', [OrderController::class, 'show']);
});