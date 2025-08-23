<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Override the login method to check user status
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // CUSTOM: Check if user exists and is active before attempting login
        $user = \App\Models\User::where($this->username(), $request->input($this->username()))->first();
        
        if ($user && !$user->is_active) {
            // User exists but is inactive
            \Log::warning('Inactive user attempted login', [
                'email' => $request->input($this->username()),
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Increment login attempts for security
            $this->incrementLoginAttempts($request);

            // Return with specific error message
            throw ValidationException::withMessages([
                $this->username() => ['Your account has been disabled by your System Administrator. Please contact support for assistance.'],
            ]);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
                // Clear any intended URL that might be causing issues
                $request->session()->forget('url.intended');
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Override attemptLogin to add additional checks
     */
    protected function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        
        // First check if the user exists and is active
        $user = \App\Models\User::where($this->username(), $credentials[$this->username()])->first();
        
        if (!$user) {
            return false; // User doesn't exist
        }
        
        if (!$user->is_active) {
            return false; // User is inactive - this will be caught in login() method above
        }

        // Attempt normal authentication
        $loginSuccessful = $this->guard()->attempt($credentials, $request->boolean('remember'));
        
        if ($loginSuccessful) {
            // Update last login timestamp
            $user->update(['last_login_at' => now()]);
            
            \Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        
        return $loginSuccessful;
    }

    /**
     * Override to ensure redirect to dashboard
     */
    protected function sendLoginResponse(Request $request): RedirectResponse
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        // Log the successful login
        \Log::info('User login successful, redirecting to dashboard', [
            'user_id' => $this->guard()->user()->id,
            'email' => $this->guard()->user()->email,
            'intended_url' => $request->session()->get('url.intended'),
        ]);

        // Always redirect to dashboard
        return redirect('/dashboard')->with('success', 'Welcome back!');
    }

    /**
     * Override the failed login response to provide better error messages
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Check if user exists but password is wrong
        $user = \App\Models\User::where($this->username(), $request->input($this->username()))->first();
        
        if ($user && !$user->is_active) {
            // This should be caught earlier, but just in case
            throw ValidationException::withMessages([
                $this->username() => ['Your account has been disabled by your System Administrator. Please contact support for assistance.'],
            ]);
        }

        // Standard failed login message
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Get the post-login redirect path.
     */
    public function redirectTo(): string
    {
        return '/dashboard';
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user): RedirectResponse
    {
        // Double-check user is active (shouldn't be needed but for safety)
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->withErrors([
                'email' => 'Your account has been disabled by your System Administrator.'
            ]);
        }

        return redirect('/dashboard');
    }

    /**
     * Override logout to add logging
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            \Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/');
    }
}