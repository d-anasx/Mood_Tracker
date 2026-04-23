<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class GuestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'pending') {
            return redirect()->route('waiting.approval');
        }
        if (Auth::check() && Auth::user()->status === 'blocked') {
            return redirect()->route('account.blocked');
        }
        if (Auth::check() && Auth::user()->status === 'active') {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}