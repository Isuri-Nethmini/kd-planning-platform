<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\LoginLog;
use App\Support\PasswordPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lets any admin change their own password.
 *
 * Also the only page an account can reach while it holds a temporary
 * password — see the forced-change check in the AdminAuth middleware.
 */
class PasswordController extends Controller
{
    public function edit(): View
    {
        $admin = AdminUser::findOrFail(session('admin_id'));

        return view('admin.password.edit', [
            'forced' => $admin->must_change_password,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = AdminUser::findOrFail(session('admin_id'));

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', PasswordPolicy::rules()],
        ]);

        // Proves the person at the keyboard is the account holder, not someone
        // who found an unlocked screen.
        if (! Hash::check($validated['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        // Re-using the temporary password would defeat the forced change.
        if (Hash::check($validated['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'password' => 'Your new password must be different from your current one.',
            ]);
        }

        $wasForced = $admin->must_change_password;

        $admin->update([
            'password'             => $validated['password'],   // hashed by the model cast
            'must_change_password' => false,
            'password_changed_at'  => now(),
        ]);

        // A new session ID after a credential change, so any session captured
        // before the change cannot be reused.
        $request->session()->regenerate();

        LoginLog::recordAccountEvent(
            $admin,
            LoginLog::PASSWORD_CHANGED,
            $request,
            $wasForced ? 'Replaced temporary password' : 'Changed own password'
        );

        return redirect('/admin/dashboard')->with('success', 'Your password has been changed.');
    }
}
