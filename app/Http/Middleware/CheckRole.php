<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            \Log::info('CheckRole: User not authenticated', [
                'url' => $request->url(),
                'route' => $request->route()?->getName(),
                'required_roles' => $roles,
                'session_id' => $request->session()?->getId(),
                'ip' => $request->ip(),
            ]);
            
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user account is active
        if (!$user->is_active) {
            \Log::warning('CheckRole: Inactive user attempted access', [
                'user_id' => $user->id,
                'email' => $user->email,
                'url' => $request->url(),
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Force logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Return with specific message about account being disabled
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been disabled by your System Administrator. Please contact support for assistance.'
            ])->with('error', 'Your account has been disabled by your System Administrator.');
        }

        // Check role permissions
        if (!empty($roles) && !in_array($user->role, $roles)) {
            \Log::warning('CheckRole: User lacks required role', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'required_roles' => $roles,
                'url' => $request->url(),
                'route' => $request->route()?->getName(),
                'ip' => $request->ip(),
            ]);
            
            // Check if it's an AJAX request
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized action.',
                    'message' => 'You do not have permission to perform this action.'
                ], 403);
            }
            
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}