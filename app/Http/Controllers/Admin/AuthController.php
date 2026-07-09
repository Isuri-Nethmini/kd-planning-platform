<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $admin = AdminUser::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
        }

        $request->session()->put('admin_id', $admin->id);
        $request->session()->put('admin_name', $admin->name);
        $request->session()->put('admin_role', $admin->role);

        $admin->update(['last_login_at' => now()]);

        return redirect('/admin/dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['admin_id', 'admin_name', 'admin_role']);
        return redirect('/admin/login')->with('success', 'Logged out successfully.');
    }
}
