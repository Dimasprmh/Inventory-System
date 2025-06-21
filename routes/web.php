<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

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

// --- USER MANAGEMENT (KHUSUS ADMIN) ---
Route::get('/usermanagement', function () {
    return view('usermanagement.usermanagement'); 
})->name('usermanagement');

Route::get('/403', function () {
    return response()->view('errors.403', [], 403);
})->name('403');

Route::get('/activity-log', function () {
    return view('activity_log.activitylog'); 
});