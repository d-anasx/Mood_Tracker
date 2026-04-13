<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    // Redirect to Google for authentication
    
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists by google_id
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if (!$user) {
                // Check if user exists by email
                $user = User::where('email', $googleUser->getEmail())->first();
                
                if ($user) {
                    // User exists but didn't have google_id - link the account
                    $user->update(['google_id' => $googleUser->getId()]);
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password' => Hash::make(Str::random(24)),
                        'avatar' => $this->getRandomAvatar(),
                        'role_id' => 2, // User role
                        'status' => 'pending', // Requires admin approval
                    ]);
                }
            }
            
            // Check user status
            if ($user->status === 'pending') {
                return redirect()->route('login')
                    ->with('error', 'Your account is pending approval. Please wait for admin activation.');
            }
            
            if ($user->status === 'blocked') {
                return redirect()->route('login')
                    ->with('error', 'Your account has been blocked. Please contact support.');
            }
            
            // Login the user
            Auth::login($user, true);
            
            return redirect()->intended('/dashboard');
            
        } catch (\Exception $e) {
            \Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Google authentication failed. Please try again.');
        }
    }
    
    /**
     * Get random avatar emoji
     */
    private function getRandomAvatar()
    {
        $avatars = ['🌙', '🌸', '☀️', '🌊', '🦋', '🌿', '⭐', '🔮', '😊', '🌟', '💫', '✨'];
        return $avatars[array_rand($avatars)];
    }
}