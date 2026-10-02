<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\LoginLog;
use App\Support\PasswordPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $admins = AdminUser::orderByDesc('role')->orderBy('name')->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_users,email',
            'role'  => 'required|in:primary,secondary',
        ]);

        // The system generates the password, so the admin creating the account
        // never knows it. The account is flagged, so the holder must replace
        // it with their own on first sign-in.
        $temporary = PasswordPolicy::temporary();

        $admin = AdminUser::create([
            ...$validated,
            'password'             => $temporary,   // hashed by the model cast
            'must_change_password' => true,
        ]);

        LoginLog::recordAccountEvent($admin, LoginLog::ACCOUNT_CREATED, $request,
            'Created by ' . session('admin_name'));

        $emailed = $this->sendTemporaryPassword($admin, $temporary, isNew: true);

        return redirect('/admin/admins')->with(
            'success',
            $emailed
                ? "Account created. A temporary password was emailed to {$admin->email}."
                : "Account created, but the email could not be sent. Check the mail settings, then use Reset password."
        );
    }

    public function edit(AdminUser $admin): View
    {
        return view('admin.admins.form', compact('admin'));
    }

    public function update(Request $request, AdminUser $admin): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('admin_users', 'email')->ignore($admin->id)],
            'role'     => 'required|in:primary,secondary',
        ]);

        // Never let the last primary admin demote themselves — that would leave
        // the system with nobody able to manage accounts.
        if ($admin->role === 'primary'
            && $validated['role'] !== 'primary'
            && AdminUser::where('role', 'primary')->count() === 1) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'This is the only primary admin. Promote another admin to primary first.']);
        }

        $admin->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'role'  => $validated['role'],
        ]);

        // Keep the sidebar name/role fresh if the admin edited their own account.
        if (session('admin_id') === $admin->id) {
            session([
                'admin_name' => $admin->name,
                'admin_role' => $admin->role,
            ]);
        }

        return redirect('/admin/admins')->with('success', 'Admin account updated.');
    }

    /**
     * Issues a new temporary password, e.g. when someone has forgotten theirs.
     *
     * Applies immediately: the forced-change check reads the database on every
     * request, so even an account that is currently logged in is sent to the
     * change-password page on its next click.
     */
    public function resetPassword(Request $request, AdminUser $admin): RedirectResponse
    {
        if (session('admin_id') === $admin->id) {
            return back()->with('error', 'Use Change Password to change your own password.');
        }

        $temporary = PasswordPolicy::temporary();

        $admin->update([
            'password'             => $temporary,
            'must_change_password' => true,
        ]);

        LoginLog::recordAccountEvent($admin, LoginLog::PASSWORD_RESET, $request,
            'Reset by ' . session('admin_name'));

        $emailed = $this->sendTemporaryPassword($admin, $temporary, isNew: false);

        return redirect('/admin/admins')->with(
            'success',
            $emailed
                ? "A new temporary password was emailed to {$admin->email}."
                : "Password reset, but the email could not be sent. Check the mail settings."
        );
    }

    public function destroy(Request $request, AdminUser $admin): RedirectResponse
    {
        if (session('admin_id') === $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($admin->role === 'primary' && AdminUser::where('role', 'primary')->count() === 1) {
            return back()->with('error', 'Cannot delete the only primary admin.');
        }

        // Logged before deleting, while the account still exists to attach to.
        LoginLog::recordAccountEvent($admin, LoginLog::ACCOUNT_DELETED, $request,
            'Deleted by ' . session('admin_name'));

        $admin->delete();

        return redirect('/admin/admins')->with('success', 'Admin account removed.');
    }

    /**
     * Emails a temporary password.
     *
     * The panel asked that staff credentials be delivered by email. Sending a
     * plain password is only acceptable because it is temporary: it works
     * once, and stops working the moment the holder sets their own.
     *
     * Returns false rather than throwing, so a mail failure never loses the
     * account that was just created.
     */
    private function sendTemporaryPassword(AdminUser $admin, string $temporary, bool $isNew): bool
    {
        try {
            $opening = $isNew
                ? "An administrator account has been created for you on the KD Planning & Design website."
                : "A new temporary password has been issued for your KD Planning & Design admin account.";

            Mail::raw(
                "Hello {$admin->name},\n\n"
                . "{$opening}\n\n"
                . "Sign in here:        " . url('/admin/login') . "\n"
                . "Email:               {$admin->email}\n"
                . "Temporary password:  {$temporary}\n"
                . "Role:                " . ($admin->isPrimary() ? 'Primary administrator' : 'Staff administrator') . "\n\n"
                . "This password is temporary. When you sign in you will be asked to choose\n"
                . "your own, and this one will stop working.\n\n"
                . "Do not forward this email. If you were not expecting it, contact\n"
                . "KD Planning & Design.\n\n"
                . "KD Planning & Design",
                function ($message) use ($admin, $isNew) {
                    $message->to($admin->email)->subject(
                        $isNew ? 'Your KD Planning & Design admin account'
                               : 'Your KD Planning & Design temporary password'
                    );
                }
            );

            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
