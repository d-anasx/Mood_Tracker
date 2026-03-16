<?php

use Illuminate\Support\Facades\Route;



Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');