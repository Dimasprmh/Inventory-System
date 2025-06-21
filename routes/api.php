<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductItemController;
use App\Http\Controllers\Api\BarangMasukController;
use App\Http\Controllers\Api\BarangKeluarController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ActivityLogController;

// AUTH ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// USER & ADMIN – Akses umum (login required)
Route::middleware('auth:sanctum')->group(function () {

    // Get user info + menu
    Route::get('/me', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'role' => $request->user()->role,
            'menus' => $request->user()->getSidebarMenu()
        ]);
    });

    // Produk (read-only)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/items', [ProductItemController::class, 'getAllItems']);
    Route::get('/products/{product}/items', [ProductItemController::class, 'index']);

    // Barang Masuk & Keluar
    Route::get('/barang-masuk', [BarangMasukController::class, 'index']);
    Route::post('/barang-masuk', [BarangMasukController::class, 'store']);
    Route::get('/barang-keluar', [BarangKeluarController::class, 'index']);
    Route::post('/barang-keluar', [BarangKeluarController::class, 'store']);

    // Statistik & Grafik Dashboard 
    Route::get('/dashboard/statistik', [DashboardController::class, 'statistik']);
    Route::get('/dashboard/grafik-bulanan', [DashboardController::class, 'grafikBarangBulanan']);

    // Log aktivitas akun
    Route::get('/my-activity-log', [ActivityLogController::class, 'index']);
});

// ADMIN ONLY – Kelola data
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Produk
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Item
    Route::post('/products/{product}/items', [ProductItemController::class, 'store']);
    Route::put('/products/{product}/items/{item}', [ProductItemController::class, 'update']);
    Route::delete('/products/{product}/items/{item}', [ProductItemController::class, 'destroy']);

    // User Management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
