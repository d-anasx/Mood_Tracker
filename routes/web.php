<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoodEntryController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// ── Guest ──
Route::middleware('guest')->group(function () {
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
});

// ── Auth ──
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mood entries
    Route::get('/mood/create',           [MoodEntryController::class, 'create'])->name('mood.create');
    Route::post('/mood',                 [MoodEntryController::class, 'store'])->name('mood.store');
    Route::get('/mood/{moodEntry}/edit', [MoodEntryController::class, 'edit'])->name('mood.edit');
    Route::patch('/mood/{moodEntry}',    [MoodEntryController::class, 'update'])->name('mood.update');

    // AI Quote — called via AJAX from dashboard / mood form
    Route::post('/quote/generate', [QuoteController::class, 'generate'])->name('quote.generate');

    Route::get('/profile', fn() => view('profile'))->name('profile');
});

// ── Admin ──
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', fn() => 'Admin — coming soon')->name('users.index');
});