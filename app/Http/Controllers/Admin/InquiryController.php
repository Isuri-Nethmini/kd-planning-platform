<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Inquiry::with(['housePlan', 'completedProject'])->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        $inquiries = $query->paginate(15)->withQueryString();

        // One grouped query rather than one COUNT per pipeline stage.
        $byStatus = Inquiry::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = ['all' => $byStatus->sum()];
        foreach (array_keys(Inquiry::STATUSES) as $status) {
            $counts[$status] = $byStatus[$status] ?? 0;
        }

        return view('admin.inquiries.index', compact('inquiries', 'counts'));
    }

    public function show(Inquiry $inquiry): View
    {
        // Opening a new inquiry advances it to "read" so the dashboard badge
        // reflects what the admin has actually looked at.
        if ($inquiry->status === 'new') {
            $inquiry->update(['status' => 'read']);
        }

        $inquiry->load(['housePlan', 'completedProject']);

        return view('admin.inquiries.show', compact('inquiry'));
    }

    /**
     * Updates the pipeline stage, the quoted construction figure and the
     * admin's private follow-up notes. One form, one request.
     */
    public function updateStatus(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status'        => ['required', Rule::in(array_keys(Inquiry::STATUSES))],
            'quoted_amount' => 'nullable|numeric|min:0|max:999999999',
            'admin_notes'   => 'nullable|string|max:5000',
        ]);

        // Stamp the first time this inquiry reached a stage past "read",
        // so response time can be measured later.
        if (is_null($inquiry->responded_at)
            && in_array($validated['status'], ['quoted', 'converted', 'closed'], true)) {
            $validated['responded_at'] = now();
        }

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry updated — now marked as ' . $inquiry->fresh()->status_label . '.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect('/admin/inquiries')->with('success', 'Inquiry deleted.');
    }
}
