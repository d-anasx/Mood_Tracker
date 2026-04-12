<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoodEntryController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\AnalyticsController;

// ── Redirect root to login ──
Route::get('/', function () {
    return redirect()->route('login');
});

// ══════════════════════════════════════════════════════════
// GUEST ROUTES
// ══════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ══════════════════════════════════════════════════════════
// AUTHENTICATED ROUTES
// ══════════════════════════════════════════════════════════
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Mood entries (including AI journal analysis)
    Route::get('/mood/create', [MoodEntryController::class, 'create'])->name('mood.create');
    Route::post('/mood', [MoodEntryController::class, 'store'])->name('mood.store');
    Route::get('/mood/{id}/edit', [MoodEntryController::class, 'edit'])->name('mood.edit');
    Route::put('/mood/{id}', [MoodEntryController::class, 'update'])->name('mood.update');
    
    // AI Journal Analysis endpoint
    Route::post('/mood/analyze-journal', [MoodEntryController::class, 'analyzeJournal'])->name('mood.analyze');
    
    // AI Quote generation (AJAX)
    Route::post('/quote/generate', [QuoteController::class, 'generate'])->name('quote.generate');
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    
    // Profile (placeholder)
    Route::get('/profile', function() {
        return view('profile');
    })->name('profile');
});

// ══════════════════════════════════════════════════════════
// ADMIN ROUTES
// ══════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', function() {
        return 'Admin user management - coming soon';
    })->name('users.index');
});