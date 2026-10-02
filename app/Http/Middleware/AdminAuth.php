<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /** Log an idle admin out after this many minutes of inactivity. */
    private const IDLE_TIMEOUT_MINUTES = 30;

    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_id')) {
            return redirect('/admin/login')
                ->with('error', 'Please log in to access the admin panel.');
        }

        // ── Idle session timeout ──────────────────────────────────
        // The client's office machine is shared, so an unattended admin
        // session left open is a real risk. Each request refreshes the
        // timestamp; 30 minutes of inactivity ends the session.
        $lastSeen = session('admin_last_seen');

        if ($lastSeen && now()->timestamp - $lastSeen > self::IDLE_TIMEOUT_MINUTES * 60) {
            $request->session()->flush();
            $request->session()->regenerate();

            return redirect('/admin/login')
                ->with('error', 'Your session expired after '
                    . self::IDLE_TIMEOUT_MINUTES . ' minutes of inactivity. Please log in again.');
        }

        session(['admin_last_seen' => now()->timestamp]);

        // ── The account must still exist ─────────────────────────
        // The session only stores an ID. Without this check, an admin who was
        // deleted while logged in would keep working until their session ended.
        $admin = AdminUser::find(session('admin_id'));

        if (! $admin) {
            $request->session()->flush();
            $request->session()->regenerate();

            return redirect('/admin/login')
                ->with('error', 'Your account is no longer active.');
        }

        // ── Forced password change ────────────────────────────────
        // An account holding a temporary password may only reach the
        // change-password page (and log out) until a real one is set. Read
        // from the database on every request, so a reset by the primary admin
        // applies immediately — even to someone already logged in.
        if ($admin->must_change_password
            && ! $request->is('admin/password')
            && ! $request->is('admin/logout')) {
            return redirect('/admin/password')
                ->with('error', 'You are using a temporary password. Set your own password to continue.');
        }

        $response = $next($request);

        // Admin pages must never be served from the browser's back-forward
        // cache after logout.
        //
        // Set on the headers bag rather than with withHeaders(): that helper
        // only exists on Illuminate responses, and CSV report downloads return
        // a Symfony StreamedResponse, which would fatal.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
