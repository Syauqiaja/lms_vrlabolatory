<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\AuthController;

Route::post('/register', [AuthController::class, 'register']); // @scribe
Route::post('/login', [AuthController::class, 'login']);       // @scribe

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']); // @scribe
    Route::post('/logout', [AuthController::class, 'logout']);  // @scribe
});
