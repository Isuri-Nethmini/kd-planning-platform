<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to primary (owner) admins only.
 *
 * Runs after AdminAuth, so a session is guaranteed to exist by this point.
 * Protects admin accounts, analytics, the security log and commercial reports.
 */
class PrimaryAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('admin_role') !== 'primary') {
            // Recorded in the security log, so the owner can see it in the
            // admin panel alongside logins and lockouts.
            LoginLog::recordAccessDenied($request);

            // Also written to the application log for developer diagnostics.
            Log::warning('Blocked access to a primary-admin page', [
                'admin_id'   => session('admin_id'),
                'admin_name' => session('admin_name'),
                'role'       => session('admin_role'),
                'page'       => '/' . $request->path(),
                'ip'         => $request->ip(),
            ]);

            return redirect('/admin/dashboard')
                ->with('error', 'You do not have permission to view that page. It is available to the primary admin only.');
        }

        return $next($request);
    }
}
