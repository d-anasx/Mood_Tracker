<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Allow access to these routes even if pending/blocked
            $allowedRoutes = [
                'logout',
                'notifications.index',
                'notifications.mark-read',
                'waiting.approval',
                'account.blocked'
            ];
            
            $currentRoute = $request->route()->getName();
            
            // If user is pending and not on allowed routes
            if ($user->status === 'pending' && !in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('waiting.approval');
            }
            
            // If user is blocked and not on allowed routes
            if ($user->status === 'blocked' && !in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('account.blocked');
            }
        }
        
        return $next($request);
    }
}