<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ProductItemController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json($request->user());
    });
    Route::apiResource('/products', ProductController::class);
    Route::get('/products/{product}/items', [ProductItemController::class, 'index']);
    Route::post('/products/{product}/items', [ProductItemController::class, 'store']);
    Route::delete('/products/{product}/items/{item}', [ProductItemController::class, 'destroy']);
    Route::put('/products/{product}/items/{item}', [ProductItemController::class, 'update']);
});
