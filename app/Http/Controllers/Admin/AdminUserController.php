<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admin_users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:primary,secondary',
        ]);

        // The 'hashed' cast on the model handles encryption.
        AdminUser::create($validated);

        return redirect('/admin/admins')->with('success', 'Admin account created.');
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
            'password' => 'nullable|string|min:8|confirmed',
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
}
