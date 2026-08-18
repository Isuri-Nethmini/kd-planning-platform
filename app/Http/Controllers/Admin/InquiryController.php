<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Inquiry::with('housePlan')->latest();

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

        $counts = [
            'all'       => Inquiry::count(),
            'new'       => Inquiry::where('status', 'new')->count(),
            'read'      => Inquiry::where('status', 'read')->count(),
            'responded' => Inquiry::where('status', 'responded')->count(),
        ];

        return view('admin.inquiries.index', compact('inquiries', 'counts'));
    }

    public function show(Inquiry $inquiry): View
    {
        // Opening an inquiry marks it as read, so the "new" badge on the
        // dashboard reflects what the admin has actually looked at.
        if ($inquiry->status === 'new') {
            $inquiry->update(['status' => 'read']);
        }

        $inquiry->load('housePlan');

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function updateStatus(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,read,responded',
        ]);

        $inquiry->update($validated);

        return back()->with('success', 'Inquiry marked as ' . $validated['status'] . '.');
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $inquiry->delete();

        return redirect('/admin/inquiries')->with('success', 'Inquiry deleted.');
    }
}
