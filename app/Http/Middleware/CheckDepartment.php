<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDepartment
{
    public function handle(Request $request, Closure $next, $departmentCode = null)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($departmentCode && $user->department->code !== $departmentCode) {
            abort(403, 'You do not have access to this department.');
        }

        return $next($request);
    }
}
