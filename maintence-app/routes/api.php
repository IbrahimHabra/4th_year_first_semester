<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\RecordPartController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    // Dashboard / Home Page
    Route::prefix('dashboard')->group(function () {
        Route::get('/low-stock-parts', [DashboardController::class, 'lowStockParts']);
        Route::get('/pending-orders', [DashboardController::class, 'pendingOrders']);
    });

    // Orders
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);

    // Member Management
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::post('/members', [MemberController::class, 'store']);
    Route::put('/members/{id}', [MemberController::class, 'update']);

    // Companies  
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);

    // Device Models  
    Route::get('/models', [DeviceModelController::class, 'index']);
    Route::get('/models/{id}', [DeviceModelController::class, 'show']);
    Route::post('/models', [DeviceModelController::class, 'store']);
    Route::put('/models/{id}', [DeviceModelController::class, 'update']);

    // Parts  
    Route::get('/parts', [PartController::class, 'index']);
    Route::get('/parts/{id}', [PartController::class, 'show']);
    Route::post('/parts', [PartController::class, 'store']);
    Route::put('/parts/{id}', [PartController::class, 'update']);
    Route::delete('/parts/{id}', [PartController::class, 'destroy']);

    // Record Parts
    Route::get('/record-parts', [RecordPartController::class, 'index']);
    Route::get('/record-parts/{id}', [RecordPartController::class, 'show']);
    Route::post('/record-parts', [RecordPartController::class, 'store']);
    Route::put('/record-parts/{id}', [RecordPartController::class, 'update']);

    // Bill
    Route::put('/bills/{id}', [BillController::class, 'update']);
    Route::delete('/bills/{id}', [BillController::class, 'destroy']);

    // Order of Bill
    Route::get('/orders/{order_id}/bills', [BillController::class, 'index']);
});


