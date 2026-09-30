<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only security log: sign-ins, lockouts and blocked page access.
 * Primary admins only — enforced by PrimaryAdmin middleware on the route.
 */
class LoginLogController extends Controller
{
    public function index(Request $request): View
    {
        // Sorted by id as a tie-breaker: several events can share the same
        // second, and without it they would come out in an arbitrary order.
        $query = LoginLog::with('admin')->latest('attempted_at')->latest('id');

        match ($request->input('result', 'all')) {
            'success' => $query->successful(),
            'failed'  => $query->failed(),
            'denied'  => $query->accessDenied(),
            default   => null,
        };

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('email', 'like', "%{$term}%")
                  ->orWhere('ip_address', 'like', "%{$term}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'successful' => LoginLog::successful()->count(),
            'failed'     => LoginLog::failed()->count(),
            'failed_24h' => LoginLog::failed()->where('attempted_at', '>=', now()->subDay())->count(),
            'denied'     => LoginLog::accessDenied()->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats'));
    }
}
