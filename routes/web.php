<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MoodEntryController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\NotificationController;

// ── Redirect root to login ──
Route::get('/', function () {
    return redirect()->route('login');
});

// ══════════════════════════════════════════════════════════
// GUEST ROUTES
// ══════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Google OAuth routes
    Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

// ══════════════════════════════════════════════════════════
// AUTHENTICATED ROUTES
// ══════════════════════════════════════════════════════════
Route::middleware(['auth','check.status'])->group(function () {
    
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

    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');

    
    // Profile (placeholder)
    Route::get('/profile', function() {
        return view('profile');
    })->name('profile');
});

// ══════════════════════════════════════════════════════════
// ADMIN ROUTES
// ══════════════════════════════════════════════════════════
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management - GET for page load, POST for AJAX search
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users/search', [UserManagementController::class, 'search'])->name('users.search');
    
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/block', [UserManagementController::class, 'block'])->name('users.block');
    Route::post('/users/{id}/unblock', [UserManagementController::class, 'unblock'])->name('users.unblock');
    Route::delete('/users/{id}', [UserManagementController::class, 'delete'])->name('users.delete');
    
    // Notifications
    Route::get('/notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
});

// STATUS PAGES 

Route::middleware(['auth'])->group(function () {
    Route::get('/waiting-approval', function () {
        return view('auth.waiting');
    })->name('waiting.approval');
    
    Route::get('/account-blocked', function () {
        return view('auth.blocked');
    })->name('account.blocked');

});

//web push
Route::middleware('auth')->group(function () {
     
    Route::post('/push-subscribe', function (Illuminate\Http\Request $request) {
    try {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Non authentifié'], 401);
        }
        
        $user->updatePushSubscription(
            $request->input('endpoint'),
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );
        
        return response()->json(['success' => true, 'message' => 'Souscription sauvegardée']);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
})->name('push.subscribe');
});

