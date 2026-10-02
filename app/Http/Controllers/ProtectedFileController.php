<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use App\Models\PaymentReceipt;
use App\Models\StagePhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * The only way to reach a file on the private disk.
 *
 * Receipts and site photos are stored under storage/app/private, which the web
 * server cannot serve. Every request comes through here and is checked first.
 * Anyone not entitled gets a 404, so the response does not confirm the file
 * even exists.
 */
class ProtectedFileController extends Controller
{
    /** Receipts are financial records: the owning client or a primary admin. */
    public function receipt(PaymentReceipt $receipt): Response
    {
        $receipt->load('instalment.project');

        abort_unless(
            $this->isOwner($receipt->instalment->project->customer_id) || $this->isAdmin(primaryOnly: true),
            404
        );

        return $this->serve($receipt->file_path, $receipt->original_name);
    }

    /** Site photos: the owning client or any admin. */
    public function photo(StagePhoto $photo): Response
    {
        $photo->load('stage.project');

        abort_unless(
            $this->isOwner($photo->stage->project->customer_id) || $this->isAdmin(primaryOnly: false),
            404
        );

        return $this->serve($photo->file_path, basename($photo->file_path));
    }

    private function isOwner(int $customerId): bool
    {
        return Auth::guard('customer')->id() === $customerId;
    }

    /** Re-checked against the database, not trusted from the session alone. */
    private function isAdmin(bool $primaryOnly): bool
    {
        $admin = AdminUser::find(session('admin_id'));

        return $admin !== null && ! $admin->must_change_password && (! $primaryOnly || $admin->isPrimary());
    }

    private function serve(string $path, string $name): Response
    {
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, $name, [
            'Cache-Control'          => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
