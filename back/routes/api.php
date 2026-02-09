<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelRequestController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/travel-requests', [TravelRequestController::class, 'index']);
    Route::post('/travel-requests', [TravelRequestController::class, 'store']);
    Route::get('/travel-requests/{travelRequest}', [TravelRequestController::class, 'show']);
    Route::patch('/travel-requests/{travelRequest}/status', [TravelRequestController::class, 'updateStatus']);
});
