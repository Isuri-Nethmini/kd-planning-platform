<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = [
            'whatsapp_number'    => SystemSetting::get('whatsapp_number', ''),
            'notification_email' => SystemSetting::get('notification_email', ''),
        ];

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_number'    => ['required', 'string', 'regex:/^[+]?[0-9\s\-\(\)]{7,20}$/'],
            'notification_email' => 'required|email|max:255',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, $value);
        }

        return back()->with('success', 'Settings saved.');
    }
}
