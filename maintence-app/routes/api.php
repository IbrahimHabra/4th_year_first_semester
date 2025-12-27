<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\PartController;
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

    // Member Management
    Route::get('/members', [MemberController::class, 'index']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::put('/members/{id}', [MemberController::class, 'update']);

    // Companies  
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);

    // Device Models  
    Route::get('/models', [DeviceModelController::class, 'index']);
    Route::post('/models', [DeviceModelController::class, 'store']);
    Route::put('/models/{id}', [DeviceModelController::class, 'update']);

    // Parts  
    Route::get('/parts', [PartController::class, 'index']);
    Route::post('/parts', [PartController::class, 'store']);
    Route::put('/parts/{id}', [PartController::class, 'update']);
    Route::delete('/parts/{id}', [PartController::class, 'destroy']);
});