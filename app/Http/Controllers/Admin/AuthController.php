<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\LoginLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /** Attempts allowed before lockout, and how long the lockout lasts. */
    private const MAX_ATTEMPTS  = 5;
    private const DECAY_SECONDS = 900; // 15 minutes

    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $key = $this->throttleKey($request);

        // ── Brute-force protection ────────────────────────────────
        // Keyed on email + IP so one attacker cannot lock out a real admin
        // from a different address.
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            // Attribute the blocked attempt to the account when it exists.
            // Passing null here made lockouts of real accounts appear as
            // "Unknown account" in the Login Activity page.
            $lockedAdminId = AdminUser::where('email', $credentials['email'])->value('id');

            LoginLog::record($credentials['email'], false, $lockedAdminId, $request);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in "
                         . ceil($seconds / 60) . " minute(s).",
            ]);
        }

        $admin = AdminUser::where('email', $credentials['email'])->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            // Logged whether or not the account exists, so probing for valid
            // email addresses is also visible in the audit trail.
            LoginLog::record($credentials['email'], false, $admin?->id, $request);

            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->except('password'));
        }

        RateLimiter::clear($key);

        // Session fixation protection: issue a new session ID on login so a
        // pre-set session cookie cannot be reused to hijack the session.
        $request->session()->regenerate();

        $request->session()->put([
            'admin_id'        => $admin->id,
            'admin_name'      => $admin->name,
            'admin_role'      => $admin->role,
            'admin_last_seen' => now()->timestamp,
        ]);

        $admin->update(['last_login_at' => now()]);

        LoginLog::record($admin->email, true, $admin->id, $request);

        return redirect('/admin/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect('/admin/login')->with('success', 'Logged out successfully.');
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
