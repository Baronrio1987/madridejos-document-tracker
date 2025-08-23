<?php
// app/Http/Middleware/Authenticate.php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if (!$request->expectsJson()) {
            // Log the authentication failure for debugging
            \Log::info('Authentication required, redirecting to login', [
                'url' => $request->url(),
                'route' => $request->route()?->getName(),
                'session_id' => $request->session()?->getId(),
                'user_agent' => $request->userAgent(),
            ]);
            
            return route('login');
        }
        
        return null;
    }
}