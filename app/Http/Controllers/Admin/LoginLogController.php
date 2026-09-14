<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Read-only audit trail of admin login activity.
 * Primary admins only — enforced by PrimaryAdmin middleware on the route.
 */
class LoginLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = LoginLog::with('admin')->latest('attempted_at');

        if ($request->filled('result') && $request->result !== 'all') {
            $query->where('successful', $request->result === 'success');
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('email', 'like', "%{$term}%")
                  ->orWhere('ip_address', 'like', "%{$term}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        $stats = [
            'total'         => LoginLog::count(),
            'successful'    => LoginLog::where('successful', true)->count(),
            'failed'        => LoginLog::failed()->count(),
            'failed_24h'    => LoginLog::failed()->where('attempted_at', '>=', now()->subDay())->count(),
        ];

        return view('admin.logs.index', compact('logs', 'stats'));
    }
}
