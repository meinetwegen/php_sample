<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('health', fn () => response()->json(['status' => 'ok']));

// Auth
Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:auth');
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:auth');
Route::post('auth/refresh', [AuthController::class, 'refresh']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('auth/me', [AuthController::class, 'me'])->middleware('auth');
Route::put('auth/me', [AuthController::class, 'updateProfile'])->middleware('auth');
Route::put('auth/password', [AuthController::class, 'changePassword'])->middleware('auth');
