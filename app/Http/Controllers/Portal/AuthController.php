<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LoginLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Client sign-in. Mirrors the admin login's protections: lockout after
 * repeated failures, a fresh session ID on success, and every attempt logged.
 */
class AuthController extends Controller
{
    private const MAX_ATTEMPTS  = 5;
    private const DECAY_SECONDS = 900;   // 15 minutes

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::guard('customer')->check()) {
            return redirect('/portal');
        }

        return view('portal.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Keyed on email and IP together, so an attacker elsewhere cannot lock
        // a real client out of their own account.
        $key = 'portal-login|' . strtolower($credentials['email']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            LoginLog::recordCustomer($credentials['email'], LoginLog::LOCKOUT, false,
                Customer::where('email', $credentials['email'])->first(), $request);

            throw ValidationException::withMessages([
                'email' => 'Too many sign-in attempts. Please try again in '
                    . ceil(RateLimiter::availableIn($key) / 60) . ' minute(s).',
            ]);
        }

        if (! Auth::guard('customer')->attempt($credentials)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            LoginLog::recordCustomer($credentials['email'], LoginLog::LOGIN, false,
                Customer::where('email', $credentials['email'])->first(), $request);

            // The same message whether the email exists or not, so the form
            // cannot be used to discover which emails have accounts.
            throw ValidationException::withMessages([
                'email' => 'Those details do not match our records.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        $customer = Auth::guard('customer')->user();
        $customer->update(['last_login_at' => now()]);

        LoginLog::recordCustomer($customer->email, LoginLog::LOGIN, true, $customer, $request);

        return redirect('/portal');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->regenerate();

        return redirect('/portal/login')->with('success', 'You have been signed out.');
    }
}
