<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CompletedProject;
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
        // An inquiry can be anchored to a catalogue plan (?plan=) or to a
        // completed project the buyer wants something similar to (?project=).
        $plan = $request->filled('plan')
            ? HousePlan::active()->with('primaryImage')->find($request->plan)
            : null;

        $project = $request->filled('project')
            ? CompletedProject::active()->with('primaryImage')->find($request->project)
            : null;

        return view('public.inquire', compact('plan', 'project'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[+]?[0-9\s\-\(\)]{7,20}$/'],
            'house_plan_id'        => 'nullable|exists:house_plans,id',
            'completed_project_id' => 'nullable|exists:completed_projects,id',
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
                . "Regarding: {$inquiry->subject_label}\n\n"
                . "Message:\n{$inquiry->message}",
                fn($mail) => $mail
                    ->to($notificationEmail)
                    ->subject('New Inquiry — KD Planning & Design')
            );
        } catch (\Throwable $e) {
            // The inquiry is already saved, so a mail failure must not lose the
            // lead or show the buyer an error. Log it so the cause is visible.
            report($e);
        }

        return redirect('/inquire/success');
    }

    public function success(): View
    {
        return view('public.inquire-success');
    }
}
