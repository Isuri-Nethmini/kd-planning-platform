<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HousePlan;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Downloadable CSV reports.
 *
 * Streamed rather than built in memory, so a report stays constant-memory no
 * matter how many rows it covers. CSV opens directly in Excel, which is what
 * the client actually uses.
 */
class ReportController extends Controller
{
    public function page(): View
    {
        return view('admin.reports.index');
    }

    public function inquiries(Request $request): StreamedResponse
    {
        $from = $request->filled('from') ? $request->date('from')->startOfDay() : now()->subMonths(6)->startOfDay();
        $to   = $request->filled('to')   ? $request->date('to')->endOfDay()    : now()->endOfDay();

        $rows = Inquiry::with(['housePlan', 'completedProject'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $filename = 'kd-inquiries-' . $from->format('Y-m-d') . '-to-' . $to->format('Y-m-d') . '.csv';

        return $this->stream($filename, function ($out) use ($rows) {
            fputcsv($out, [
                'Reference', 'Date received', 'Name', 'Email', 'Phone',
                'Regarding', 'Requirements', 'Status', 'Quoted amount (Rs.)', 'First responded', 'Notes',
            ]);

            foreach ($rows as $r) {
                fputcsv($out, [
                    'INQ-' . str_pad($r->id, 5, '0', STR_PAD_LEFT),
                    $r->created_at->format('Y-m-d H:i'),
                    $r->name,
                    $r->email,
                    $r->phone,
                    $r->subject_label,
                    collect($r->requirements)->map(fn($v, $k) => "{$k}: {$v}")->implode('; '),
                    $r->status_label,
                    $r->quoted_amount ? number_format((float) $r->quoted_amount, 2, '.', '') : '',
                    $r->responded_at?->format('Y-m-d H:i') ?? '',
                    str_replace(["\r", "\n"], ' ', (string) $r->admin_notes),
                ]);
            }
        });
    }

    public function plans(): StreamedResponse
    {
        $rows = HousePlan::withCount('inquiries')->orderByDesc('view_count')->get();

        return $this->stream('kd-plan-performance-' . now()->format('Y-m-d') . '.csv', function ($out) use ($rows) {
            fputcsv($out, [
                'Plan ref', 'Name', 'Style', 'Bedrooms', 'Bathrooms', 'Floors',
                'Floor area (sqft)', 'Plan price (Rs.)', 'Status', 'Views', 'Inquiries', 'View to inquiry %',
            ]);

            foreach ($rows as $p) {
                fputcsv($out, [
                    'PLAN-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                    $p->name,
                    $p->style,
                    $p->bedrooms,
                    $p->bathrooms,
                    $p->floors,
                    $p->floor_area,
                    number_format((float) $p->price, 2, '.', ''),
                    $p->is_active ? 'Active' : 'Hidden',
                    $p->view_count,
                    $p->inquiries_count,
                    $p->view_count > 0 ? round(($p->inquiries_count / $p->view_count) * 100, 2) : 0,
                ]);
            }
        });
    }

    public function summary(): StreamedResponse
    {
        $totalViews  = (int) HousePlan::sum('view_count');
        $total       = Inquiry::count();
        $won         = Inquiry::won()->count();

        return $this->stream('kd-summary-' . now()->format('Y-m-d') . '.csv', function ($out) use ($totalViews, $total, $won) {
            fputcsv($out, ['KD Planning & Design — Summary Report']);
            fputcsv($out, ['Generated', now()->format('Y-m-d H:i')]);
            fputcsv($out, []);
            fputcsv($out, ['Metric', 'Value']);

            $rows = [
                ['Total house plans',        HousePlan::count()],
                ['Active plans',             HousePlan::active()->count()],
                ['Total plan views',         $totalViews],
                ['Total inquiries',          $total],
                ['Converted to sales',       $won],
                ['View to inquiry rate (%)', $totalViews > 0 ? round(($total / $totalViews) * 100, 2) : 0],
                ['Inquiry to sale rate (%)', $total > 0 ? round(($won / $total) * 100, 2) : 0],
                ['Total quoted value (Rs.)', number_format((float) Inquiry::whereNotNull('quoted_amount')->sum('quoted_amount'), 2, '.', '')],
                ['Converted value (Rs.)',    number_format((float) Inquiry::won()->sum('quoted_amount'), 2, '.', '')],
            ];

            foreach ($rows as $row) {
                fputcsv($out, $row);
            }

            fputcsv($out, []);
            fputcsv($out, ['Inquiries by status']);

            foreach (Inquiry::STATUSES as $key => $label) {
                fputcsv($out, [$label, Inquiry::where('status', $key)->count()]);
            }
        });
    }

    private function stream(string $filename, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel renders Sinhala names and the Rs. sign correctly.
            fwrite($out, "\xEF\xBB\xBF");
            $writer($out);
            fclose($out);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
