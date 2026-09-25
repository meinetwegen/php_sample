<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\MagazineController as AdminMagazineController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\StatsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MagazineController;
use App\Http\Controllers\OrderController;
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

// Public catalog
Route::get('categories', [CategoryController::class, 'index']);
Route::get('magazines', [MagazineController::class, 'index']);
Route::get('magazines/{magazine}', [MagazineController::class, 'show']);

// Cart
Route::get('cart', [CartController::class, 'show'])->middleware('auth');
Route::post('cart', [CartController::class, 'store'])->middleware('auth');
Route::delete('cart', [CartController::class, 'clear'])->middleware('auth');
Route::put('cart/{magazine}', [CartController::class, 'update'])->middleware('auth');
Route::delete('cart/{magazine}', [CartController::class, 'destroy'])->middleware('auth');

// Orders
Route::get('orders', [OrderController::class, 'index'])->middleware('auth');
Route::post('orders', [OrderController::class, 'store'])->middleware('auth');
Route::get('orders/{order}', [OrderController::class, 'show'])->middleware('auth');
Route::post('orders/{order}/pay', [OrderController::class, 'pay'])->middleware('auth');
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->middleware('auth');

// Admin
Route::get('admin/stats', StatsController::class)->middleware(['auth', 'admin']);
Route::get('admin/magazines', [AdminMagazineController::class, 'index'])->middleware(['auth', 'admin']);
Route::post('admin/magazines', [AdminMagazineController::class, 'store'])->middleware(['auth', 'admin']);
Route::put('admin/magazines/{magazine}', [AdminMagazineController::class, 'update'])->middleware(['auth', 'admin']);
Route::delete('admin/magazines/{magazine}', [AdminMagazineController::class, 'destroy'])->middleware(['auth', 'admin']);
Route::post('admin/categories', [AdminCategoryController::class, 'store'])->middleware(['auth', 'admin']);
Route::put('admin/categories/{category}', [AdminCategoryController::class, 'update'])->middleware(['auth', 'admin']);
Route::delete('admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->middleware(['auth', 'admin']);
Route::get('admin/orders', [AdminOrderController::class, 'index'])->middleware(['auth', 'admin']);
Route::patch('admin/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->middleware(['auth', 'admin']);
