<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DivisiController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::view('/', 'welcome')->name('home');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::resource('divisi', DivisiController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);
});