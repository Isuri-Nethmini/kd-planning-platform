<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admin_users,email',
            'password' => ['required', 'confirmed', $this->passwordRules()],
            'role'     => 'required|in:primary,secondary',
        ]);

        // The 'hashed' cast on the model handles encryption.
        $admin = AdminUser::create($validated);

        $emailed = $this->sendCredentials($admin, $validated['password']);

        return redirect('/admin/admins')->with(
            'success',
            $emailed
                ? "Admin account created. Login details were emailed to {$admin->email}."
                : "Admin account created, but the credentials email could not be sent. Check the mail configuration."
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
            'password' => ['nullable', 'confirmed', $this->passwordRules()],
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
            // Blank password field means "leave the current password alone".
            ...(filled($validated['password']) ? ['password' => $validated['password']] : []),
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

    public function destroy(Request $request, AdminUser $admin): RedirectResponse
    {
        if (session('admin_id') === $admin->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($admin->role === 'primary' && AdminUser::where('role', 'primary')->count() === 1) {
            return back()->with('error', 'Cannot delete the only primary admin.');
        }

        $admin->delete();

        return redirect('/admin/admins')->with('success', 'Admin account removed.');
    }

    /**
     * Password policy for admin accounts.
     *
     * Raised from a bare 8-character minimum after the interim review, where
     * the panel asked for stronger admin security. Laravel's Password rule
     * enforces mixed case, a digit and a symbol, and checks the password
     * against known breach data through the Have I Been Pwned k-anonymity API.
     */
    private function passwordRules(): Password
    {
        return Password::min(10)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();
    }

    /**
     * Emails login details to a newly created admin.
     *
     * The panel asked that staff credentials be delivered by email rather than
     * read out or written down. Returns false rather than throwing, so a mail
     * failure never loses the account that was just created.
     */
    private function sendCredentials(AdminUser $admin, string $plainPassword): bool
    {
        try {
            $loginUrl = url('/admin/login');

            Mail::raw(
                "Hello {$admin->name},\n\n"
                . "An administrator account has been created for you on the "
                . "KD Planning & Design website.\n\n"
                . "Sign in here: {$loginUrl}\n"
                . "Email:    {$admin->email}\n"
                . "Password: {$plainPassword}\n"
                . "Role:     " . ($admin->role === 'primary' ? 'Primary administrator' : 'Staff administrator') . "\n\n"
                . "Please sign in and change this password as soon as possible.\n"
                . "Do not forward this email to anyone.\n\n"
                . "KD Planning & Design",
                function ($message) use ($admin) {
                    $message->to($admin->email)
                            ->subject('Your KD Planning & Design admin account');
                }
            );

            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
