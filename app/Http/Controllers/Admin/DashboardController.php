<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HousePlan;
use App\Models\Inquiry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_plans'       => HousePlan::count(),
            'active_plans'      => HousePlan::active()->count(),
            'total_inquiries'   => Inquiry::count(),
            'new_inquiries'     => Inquiry::where('status', 'new')->count(),
            'this_week'         => Inquiry::where('created_at', '>=', now()->startOfWeek())->count(),
            'converted'         => Inquiry::won()->count(),
        ];

        $recentInquiries = Inquiry::with('housePlan')
            ->latest()
            ->take(8)
            ->get();

        // View counts are commercial performance data. Staff admins never
        // receive it — the query does not run for them, so it cannot leak
        // through the view.
        $mostViewed = collect();

        if (session('admin_role') === 'primary') {
            // Only plans that have actually been viewed: a ranking of zeros
            // would list every plan and tell the owner nothing.
            $mostViewed = HousePlan::active()
                ->where('view_count', '>', 0)
                ->orderByDesc('view_count')
                ->take(5)
                ->get();
        }

        return view('admin.dashboard', compact('stats', 'recentInquiries', 'mostViewed'));
    }
}
