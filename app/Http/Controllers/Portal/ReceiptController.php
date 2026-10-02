<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PaymentInstalment;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Upload of an offline-payment receipt.
 *
 * There is no payment gateway by the client's choice: customers pay by bank
 * transfer and upload proof here, and an admin verifies it.
 */
class ReceiptController extends Controller
{
    public function store(Request $request, PaymentInstalment $instalment): RedirectResponse
    {
        $instalment->load('project', 'latestReceipt');

        abort_unless($instalment->project->customer_id === Auth::guard('customer')->id(), 404);

        // Not while one is awaiting review, and not once it is paid.
        if (! $instalment->can_upload) {
            return back()->with('error', 'A receipt for this instalment is already '
                . ($instalment->status === 'paid' ? 'verified.' : 'being reviewed.'));
        }

        $validated = $request->validate([
            // Type is checked from the file's contents, not just its name.
            'receipt'     => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'amount_paid' => 'required|numeric|min:1|max:999999999',
            'paid_on'     => 'required|date|before_or_equal:today',
            'reference'   => 'nullable|string|max:100',
        ]);

        $file = $request->file('receipt');

        // The "local" disk is private: storage/app/private is not reachable
        // from the web. The file is served only by ProtectedFileController.
        $path = $file->store("receipts/project-{$instalment->client_project_id}", 'local');

        $receipt = $instalment->receipts()->create([
            'file_path'     => $path,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 200),
            'amount_paid'   => $validated['amount_paid'],
            'paid_on'       => $validated['paid_on'],
            'reference'     => $validated['reference'] ?? null,
            'status'        => 'pending',
        ]);

        Notifier::receiptUploaded($receipt);

        return back()->with('success', "Receipt uploaded for \"{$instalment->label}\". We'll let you know once it's verified.");
    }
}
