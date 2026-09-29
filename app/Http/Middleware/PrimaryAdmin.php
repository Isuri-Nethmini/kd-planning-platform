<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to primary (owner) admins only.
 *
 * Runs after AdminAuth, so a session is guaranteed to exist by this point.
 * Protects admin accounts, analytics, login activity and commercial reports.
 */
class PrimaryAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('admin_role') !== 'primary') {
            // Record the blocked attempt, so the owner can see if a staff
            // account is trying to reach pages it should not.
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
