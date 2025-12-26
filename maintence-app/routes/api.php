<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Dashboard / Home Page
    Route::prefix('dashboard')->group(function () {
        Route::get('/low-stock-parts', [DashboardController::class, 'lowStockParts']);
        Route::get('/pending-orders', [DashboardController::class, 'pendingOrders']);
    });

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
});