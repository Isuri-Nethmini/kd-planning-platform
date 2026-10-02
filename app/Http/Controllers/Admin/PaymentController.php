<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProject;
use App\Models\PaymentInstalment;
use App\Models\PaymentReceipt;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Payment schedules and receipt verification. Primary admins only (enforced by
 * the PrimaryAdmin middleware on these routes): this is the client's money.
 *
 * Every decision records who made it and when, on the receipt itself, so the
 * payment history is a permanent audit trail.
 */
class PaymentController extends Controller
{
    public function index(): View
    {
        $pending = PaymentReceipt::with('instalment.project.customer')
            ->where('status', 'pending')
            ->oldest()                       // longest-waiting first
            ->get();

        $recent = PaymentReceipt::with(['instalment.project.customer', 'reviewer'])
            ->where('status', '!=', 'pending')
            ->latest('reviewed_at')
            ->take(15)
            ->get();

        return view('admin.payments.index', compact('pending', 'recent'));
    }

    public function storeInstalment(Request $request, ClientProject $project): RedirectResponse
    {
        $validated = $request->validate([
            'label'    => 'required|string|max:100',
            'amount'   => 'required|numeric|min:1|max:999999999',
            'due_date' => 'nullable|date',
        ]);

        $project->instalments()->create([
            ...$validated,
            'sort_order' => ($project->instalments()->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', "Instalment \"{$validated['label']}\" added.");
    }

    public function destroyInstalment(PaymentInstalment $instalment): RedirectResponse
    {
        // Once a receipt exists the instalment is part of the payment record.
        if ($instalment->receipts()->exists()) {
            return back()->with('error', 'This instalment has receipts against it, so it cannot be deleted.');
        }

        $instalment->delete();

        return back()->with('success', 'Instalment removed.');
    }

    public function verify(PaymentReceipt $receipt): RedirectResponse
    {
        return $this->review($receipt, 'verified');
    }

    public function reject(Request $request, PaymentReceipt $receipt): RedirectResponse
    {
        $validated = $request->validate([
            // The client sees this, so they know what to fix.
            'rejection_reason' => 'required|string|max:255',
        ]);

        return $this->review($receipt, 'rejected', $validated['rejection_reason']);
    }

    private function review(PaymentReceipt $receipt, string $status, ?string $reason = null): RedirectResponse
    {
        // A decision is final; a rejected client uploads a new receipt instead.
        if ($receipt->status !== 'pending') {
            return back()->with('error', 'This receipt has already been reviewed.');
        }

        $receipt->update([
            'status'           => $status,
            'reviewed_by'      => session('admin_id'),
            'reviewed_at'      => now(),
            'rejection_reason' => $reason,
        ]);

        Notifier::receiptReviewed($receipt->fresh('instalment.project.customer'));

        return back()->with('success', $status === 'verified'
            ? 'Payment verified. The client has been notified.'
            : 'Receipt rejected. The client has been told why and can upload a new one.');
    }
}
