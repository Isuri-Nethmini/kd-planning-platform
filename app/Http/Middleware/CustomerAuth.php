<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the client portal.
 *
 * Uses the "customer" guard only — an admin session does not satisfy it, and
 * this session does not satisfy AdminAuth. The guard reloads the customer from
 * the database on each request, so a deleted client is signed out immediately.
 */
class CustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return redirect('/portal/login')->with('error', 'Please sign in to view your project.');
        }

        // Same rule as admins: a temporary password must be replaced first.
        if ($customer->must_change_password
            && ! $request->is('portal/password')
            && ! $request->is('portal/logout')) {
            return redirect('/portal/password')
                ->with('error', 'You are using a temporary password. Choose your own to continue.');
        }

        $response = $next($request);

        // Project details and receipts must not be cached by a shared browser.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');

        return $response;
    }
}
