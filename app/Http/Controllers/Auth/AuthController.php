<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     */
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => $request->avatar ?? '😊',
            'role_id' => 2, // User role
            'status' => 'pending', // Requires admin approval
        ]);

        return redirect()->route('login')->with('success', 'Account created! Please wait for admin approval.');
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        // Validate credentials
        $credentials = $request->only('email', 'password');

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check user status
        $user = Auth::user();

        if ($user->status === 'pending') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is pending approval. Please wait for admin activation.',
            ])->onlyInput('email');
        }

        if ($user->status === 'blocked') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account has been blocked. Please contact support.',
            ])->onlyInput('email');
        }

        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}