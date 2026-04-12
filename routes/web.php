<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoodEntryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ── Redirect root to login ──
Route::get('/', function () {
    return redirect()->route('login');
});

// ══════════════════════════════════════════════════════════
// GUEST ROUTES (unauthenticated users only)
// ══════════════════════════════════════════════════════════

Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// ══════════════════════════════════════════════════════════
// AUTHENTICATED ROUTES (active users only)
// ══════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Mood entries
    Route::get('/mood/create', [MoodEntryController::class, 'create'])->name('mood.create');
    Route::post('/mood', [MoodEntryController::class, 'store'])->name('mood.store');
    Route::get('/mood/{id}/edit', [MoodEntryController::class, 'edit'])->name('mood.edit');
    Route::put('/mood/{id}', [MoodEntryController::class, 'update'])->name('mood.update');
    
    // AI Journal Analysis (API endpoint)
    Route::post('/mood/analyze-journal', [MoodEntryController::class, 'analyzeJournal'])->name('mood.analyze');
    
    // Profile
    Route::get('/profile', function() {
        return 'Profile page - coming soon';
    })->name('profile');
    
});

// ══════════════════════════════════════════════════════════
// ADMIN ROUTES (admins only)
// ══════════════════════════════════════════════════════════

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // User management (coming next)
    Route::get('/users', function() {
        return 'Admin user management - coming soon';
    })->name('users.index');
    
});