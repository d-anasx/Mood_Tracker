<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()
                ->route('login')
                ->with('error', 'Please sign in to continue.');
        }

        $user = Auth::user();

        // if ($user->status === 'pending') {
        //     Auth::logout();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();

        //     return redirect()
        //         ->route('login')
        //         ->withErrors(['email' => 'Your account is awaiting admin approval.']);
        // }

        // if ($user->status === 'blocked') {
        //     Auth::logout();
        //     $request->session()->invalidate();
        //     $request->session()->regenerateToken();

        //     return redirect()
        //         ->route('login')
        //         ->withErrors(['email' => 'Your account has been suspended. Please contact support.']);
        // }

        return $next($request);
    }
}