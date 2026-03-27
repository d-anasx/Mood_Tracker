<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;


Route::middleware("guest")->group(function () {
 
    // Register
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
 
    // Login
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
 
});
 
// ============================================================
// LOGOUT  — must be authenticated to log out
// ============================================================
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware("auth")
    ->name('logout');
 
// ============================================================
// AUTHENTICATED USER ROUTES
// ============================================================
Route::middleware("auth")->group(function () {
 
    // Dashboard — main entry point after login
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
 
    // ── Mood Entries ──
    // Route::resource('mood-entries', MoodEntryController::class);
 
    // ── Profile ──
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
 
    // ── Settings ──
    // Route::get('/settings', [UserSettingsController::class, 'edit'])->name('settings.edit');
    // Route::patch('/settings', [UserSettingsController::class, 'update'])->name('settings.update');
 
});
 
// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(["auth", "admin"])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
 
        // Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        // Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        // Route::patch('/users/{user}/block',   [AdminUserController::class, 'block'])->name('users.block');
        // Route::patch('/users/{user}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');
        // Route::delete('/users/{user}',        [AdminUserController::class, 'destroy'])->name('users.destroy');
 
    });

Route::get('/', fn() => redirect()->route('login'));