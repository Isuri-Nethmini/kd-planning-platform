<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to primary (owner) admins only.
 *
 * Runs after AdminAuth, so a session is guaranteed to exist by this point.
 * Secondary/staff admins can manage site content but not other admin accounts.
 */
class PrimaryAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session('admin_role') !== 'primary') {
            return redirect('/admin/dashboard')
                ->with('error', 'Only the primary admin can manage admin accounts.');
        }

        return $next($request);
    }
}
