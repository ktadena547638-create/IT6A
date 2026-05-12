<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login with rate limiting protection
     * ✅ SUPPORTS: Both email and simple username (admin, sarah, alex, etc.)
     */
    public function handleLogin(Request $request): RedirectResponse
    {
        // ✅ SECURITY: Rate limit login attempts to prevent brute force
        $this->ensureIsNotRateLimited($request);

        // Validate input - accept both email format and simple usernames
        $input = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $identifier = trim($input['email']);
        $identifierLower = strtolower($identifier);

        // Use case-insensitive lookup across both name and email.
        // This allows demo logins like "admin" even if stored as "Admin".
        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$identifierLower])
            ->orWhereRaw('LOWER(name) = ?', [$identifierLower])
            ->first();

        if ($user && Hash::check($input['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            
            // ✅ CRITICAL: Check if email is verified before allowing login
            if (!$user->email_verified_at) {
                Auth::logout();
                $request->session()->invalidate();
                
                return back()->withErrors([
                    'email' => 'Please verify your email address before logging in. A verification email has been sent.',
                ])->withInput();
            }
            
            // ✅ SECURITY: Regenerate session on successful login (CSRF token + session ID)
            $request->session()->regenerate();
            
            // Clear rate limit counter on successful login
            RateLimiter::clear($this->throttleKey($request));
            
            return redirect()->route('home.index');
        }

        // Track failed attempt
        RateLimiter::hit($this->throttleKey($request), 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Ensure the login request is not rate limited
     * Maximum 5 attempts per minute per IP
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), $maxAttempts = 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }

    /**
     * Handle logout with proper session cleanup
     */
    public function logout(Request $request): RedirectResponse
    {
        // ✅ SECURITY: Invalidate session to prevent session fixation attacks
        Auth::logout();
        $request->session()->invalidate();
        
        // ✅ SECURITY: Regenerate CSRF token to prevent CSRF attacks with old session
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}

