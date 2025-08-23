<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user account is active
            if (!$user->is_active) {
                \Log::warning('CheckActiveUser: Inactive user attempted access', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'url' => $request->url(),
                    'route' => $request->route()?->getName(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()->toISOString(),
                ]);
                
                // Force logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Handle AJAX requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Account disabled',
                        'message' => 'Your account has been disabled by your System Administrator.',
                        'redirect' => route('login')
                    ], 401);
                }
                
                // Handle regular web requests
                return redirect()->route('login')
                    ->withErrors([
                        'email' => 'Your account has been disabled by your System Administrator. Please contact support for assistance.'
                    ])
                    ->with('error', 'Your account has been disabled by your System Administrator.');
            }
        }

        return $next($request);
    }
}