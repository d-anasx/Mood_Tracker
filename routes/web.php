<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoodEntryController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\AnalyticsController;

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
    
       // AI Quote — called via AJAX from dashboard / mood form
    Route::post('/quote/generate', [QuoteController::class, 'generate'])->name('quote.generate');

    // analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

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

















// <?php

// use App\Http\Controllers\Auth\AuthController;
// use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\MoodEntryController;
// use App\Http\Controllers\QuoteController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AnalyticsController;

// Route::get('/', fn() => redirect()->route('login'));

// // ── Guest ──
// Route::middleware('guest')->group(function () {
//     Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register']);
//     Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login',    [AuthController::class, 'login']);
// });

// // ── Auth ──
// Route::middleware('auth')->group(function () {

//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

//     // Mood entries
//     Route::get('/mood/create',           [MoodEntryController::class, 'create'])->name('mood.create');
//     Route::post('/mood',                 [MoodEntryController::class, 'store'])->name('mood.store');
//     Route::get('/mood/{moodEntry}/edit', [MoodEntryController::class, 'edit'])->name('mood.edit');
//     Route::patch('/mood/{moodEntry}',    [MoodEntryController::class, 'update'])->name('mood.update');

//     // AI Quote — called via AJAX from dashboard / mood form
//     Route::post('/quote/generate', [QuoteController::class, 'generate'])->name('quote.generate');

//     // analytics
//     Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

//     Route::get('/profile', fn() => view('profile'))->name('profile');
// });

// // ── Admin ──
// Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
//     Route::get('/users', fn() => 'Admin — coming soon')->name('users.index');
// });