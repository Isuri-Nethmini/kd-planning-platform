<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HousePlan;
use App\Models\Inquiry;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(): View
    {
        // ── Headline numbers ──────────────────────────────────────
        $totalViews = (int) HousePlan::sum('view_count');

        $summary = [
            'total_plans'     => HousePlan::count(),
            'active_plans'    => HousePlan::active()->count(),
            'total_views'     => $totalViews,
            'total_inquiries' => Inquiry::count(),
            'conversion'      => $totalViews > 0
                ? round((Inquiry::whereNotNull('house_plan_id')->count() / $totalViews) * 100, 2)
                : 0,
        ];

        // ── Inquiries per month, last 6 months ────────────────────
        // Built in PHP rather than SQL date functions so the same code runs
        // on MySQL in production and SQLite in testing.
        $months = collect(range(5, 0))->map(function ($back) {
            $start = Carbon::now()->startOfMonth()->subMonths($back);

            return [
                'label' => $start->format('M Y'),
                'count' => Inquiry::whereBetween('created_at', [
                    $start,
                    (clone $start)->endOfMonth(),
                ])->count(),
            ];
        });

        $peakMonth = max(1, $months->max('count'));

        // ── Leaderboards ──────────────────────────────────────────
        $topViewed = HousePlan::orderByDesc('view_count')->take(10)->get();

        // has() rather than having(): HAVING without GROUP BY is a MySQL
        // extension that SQLite rejects, and the test suite runs on SQLite.
        $topInquired = HousePlan::withCount('inquiries')
            ->has('inquiries')
            ->orderByDesc('inquiries_count')
            ->take(10)
            ->get();

        $categoryBreakdown = Category::withCount('housePlans')
            ->orderByDesc('house_plans_count')
            ->get();

        $statusBreakdown = [
            'new'       => Inquiry::where('status', 'new')->count(),
            'read'      => Inquiry::where('status', 'read')->count(),
            'responded' => Inquiry::where('status', 'responded')->count(),
        ];

        return view('admin.analytics.index', compact(
            'summary', 'months', 'peakMonth', 'topViewed',
            'topInquired', 'categoryBreakdown', 'statusBreakdown'
        ));
    }
}
