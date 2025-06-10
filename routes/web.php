<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('login');
})->name('logout');

Route::get('/products', function () {
    return view('products.product');
})->name('users.index');

Route::get('/products/{product}/items', function () {
    return view('products.item');
})->name('products.items');


