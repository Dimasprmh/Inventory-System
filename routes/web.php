<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Web\BarangMasukWebController; // ← penting

// --- AUTHENTICATION ---
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('login');
})->name('logout');

// --- DASHBOARD ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// --- PRODUK DAN ITEM ---
Route::get('/products', function () {
    return view('products.product');
})->name('users.index');

Route::get('/products/{product}/items', function () {
    return view('products.item');
})->name('products.items');

Route::get('/barang_masuk', function () {
    return view('kelola_barang.barang_masuk');
})->name('barang_masuk');

Route::get('/barang_keluar', function () {
    return view('kelola_barang.barang_keluar');
})->name('barang_keluar');
