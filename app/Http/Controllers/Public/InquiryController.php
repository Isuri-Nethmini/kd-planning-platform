<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\HousePlan;
use App\Models\Inquiry;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function create(Request $request): View
    {
        $plan = null;
        if ($request->filled('plan')) {
            $plan = HousePlan::find($request->plan);
        }
        return view('public.inquire', compact('plan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[+]?[0-9\s\-\(\)]{7,20}$/'],
            'house_plan_id' => 'nullable|exists:house_plans,id',
            'message'       => 'required|string|max:2000',
        ]);

        $inquiry = Inquiry::create($validated);

        // Email notification to admin
        $notificationEmail = SystemSetting::get('notification_email', 'kdplanning@gmail.com');

        try {
            Mail::raw(
                "New inquiry received!\n\n"
                . "From: {$inquiry->name}\n"
                . "Email: {$inquiry->email}\n"
                . "Phone: {$inquiry->phone}\n"
                . "Plan: " . ($inquiry->house_plan_id ? HousePlan::find($inquiry->house_plan_id)?->name : 'General inquiry') . "\n\n"
                . "Message:\n{$inquiry->message}",
                fn($mail) => $mail
                    ->to($notificationEmail)
                    ->subject('New Inquiry — KD Planning & Design')
            );
        } catch (\Exception $e) {
            // Silently fail — inquiry is saved to DB regardless
        }

        return redirect('/inquire/success');
    }

    public function success(): View
    {
        return view('public.inquire-success');
    }
}
