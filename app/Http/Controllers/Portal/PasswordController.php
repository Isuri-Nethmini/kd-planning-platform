<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Support\PasswordPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Same rules as the admin change-password page, shared through PasswordPolicy. */
class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('portal.password', [
            'forced' => Auth::guard('customer')->user()->must_change_password,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', PasswordPolicy::rules()],
        ]);

        if (! Hash::check($validated['current_password'], $customer->password)) {
            throw ValidationException::withMessages(['current_password' => 'Your current password is incorrect.']);
        }

        if (Hash::check($validated['password'], $customer->password)) {
            throw ValidationException::withMessages(['password' => 'Your new password must be different from your current one.']);
        }

        $wasForced = $customer->must_change_password;

        $customer->update([
            'password'             => $validated['password'],
            'must_change_password' => false,
            'password_changed_at'  => now(),
        ]);

        $request->session()->regenerate();

        LoginLog::recordCustomer($customer->email, LoginLog::PASSWORD_CHANGED, true, $customer, $request,
            $wasForced ? 'Replaced temporary password' : 'Changed own password');

        return redirect('/portal')->with('success', 'Your password has been changed.');
    }
}
