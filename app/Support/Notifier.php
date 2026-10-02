<?php

namespace App\Support;

use App\Models\Customer;
use App\Models\PaymentReceipt;
use App\Models\ProjectStage;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;

/**
 * Every client-portal email in one place.
 *
 * Each method returns false instead of throwing, so a mail failure never
 * undoes the action that triggered it — a receipt is still saved even if the
 * notification about it could not be sent.
 */
class Notifier
{
    public static function customerAccount(Customer $customer, string $temporary, bool $isNew): bool
    {
        $opening = $isNew
            ? "Your client account for the KD Planning & Design portal is ready. You can follow your\nproject's construction progress and upload payment receipts there."
            : "A new temporary password has been issued for your KD Planning & Design client account.";

        return self::send($customer->email,
            $isNew ? 'Your KD Planning & Design client portal account' : 'Your KD Planning & Design temporary password',
            "Hello {$customer->name},\n\n{$opening}\n\n"
            . "Sign in here:        " . url('/portal/login') . "\n"
            . "Email:               {$customer->email}\n"
            . "Temporary password:  {$temporary}\n\n"
            . "This password is temporary. When you sign in you will be asked to choose your\n"
            . "own, and this one will stop working.\n\n"
            . "If you were not expecting this email, please contact us.\n\n"
            . "KD Planning & Design");
    }

    /** To the business: a client has uploaded a receipt that needs checking. */
    public static function receiptUploaded(PaymentReceipt $receipt): bool
    {
        $instalment = $receipt->instalment;
        $project    = $instalment->project;

        return self::send(SystemSetting::get('notification_email', config('mail.from.address')),
            "Receipt to verify — {$project->customer->name}",
            "A client has uploaded a payment receipt.\n\n"
            . "Client:       {$project->customer->name}\n"
            . "Project:      {$project->title}\n"
            . "Instalment:   {$instalment->label} (Rs. " . number_format($instalment->amount) . ")\n"
            . "Amount paid:  Rs. " . number_format($receipt->amount_paid) . "\n"
            . "Paid on:      {$receipt->paid_on->format('d M Y')}\n"
            . "Reference:    " . ($receipt->reference ?: '—') . "\n\n"
            . "Review it here: " . url('/admin/payments') . "\n");
    }

    /** To the client: their receipt was accepted or rejected. */
    public static function receiptReviewed(PaymentReceipt $receipt): bool
    {
        $instalment = $receipt->instalment;
        $customer   = $instalment->project->customer;

        $body = $receipt->status === 'verified'
            ? "Your payment for \"{$instalment->label}\" has been verified. Thank you."
            : "We could not verify your receipt for \"{$instalment->label}\".\n\n"
              . "Reason: {$receipt->rejection_reason}\n\n"
              . "Please sign in and upload a new receipt for this instalment.";

        return self::send($customer->email,
            $receipt->status === 'verified' ? 'Payment verified' : 'Receipt needs attention',
            "Hello {$customer->name},\n\n{$body}\n\n"
            . "View your project: " . url('/portal/projects/' . $instalment->client_project_id) . "\n\n"
            . "KD Planning & Design");
    }

    /** To the client: a construction stage has started or finished. */
    public static function stageUpdated(ProjectStage $stage): bool
    {
        $project  = $stage->project;
        $customer = $project->customer;
        $what     = $stage->status === 'completed' ? 'has been completed' : 'has started';

        return self::send($customer->email,
            "Construction update: {$stage->name}",
            "Hello {$customer->name},\n\n"
            . "The \"{$stage->name}\" stage of {$project->title} {$what}.\n"
            . ($stage->note ? "\nNote from the site team: {$stage->note}\n" : '')
            . "\nSee progress and photos: " . url('/portal/projects/' . $project->id) . "\n\n"
            . "KD Planning & Design");
    }

    private static function send(string $to, string $subject, string $body): bool
    {
        try {
            Mail::raw($body, fn ($m) => $m->to($to)->subject($subject));
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }
}
