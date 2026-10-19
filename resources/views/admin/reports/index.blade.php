@extends('admin.layout')

@section('title', 'Reports')

@section('content')

<div class="mb-2">
    <h1 class="title-3d font-display text-2xl font-semibold text-ink">Reports</h1>
    <p class="text-ink/70 text-sm mt-1">
        Download your data as CSV. Files open directly in Excel.
    </p>
</div>

<div class="grid md:grid-cols-2 gap-6 mt-8">

    {{-- Inquiry report — available to all admins --}}
    <div class="bg-white border border-ink/15 rounded-sm p-6">
        <h2 class="font-display text-lg font-semibold text-ink mb-2">Inquiry Report</h2>
        <p class="text-sm text-ink/70 leading-relaxed mb-5">
            Every inquiry in a date range, with contact details, what it was about,
            pipeline status, the amount quoted and follow-up notes.
        </p>

        <form method="GET" action="/admin/reports/inquiries.csv" class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">From</label>
                    <input type="date" name="from" value="{{ now()->subMonths(6)->format('Y-m-d') }}"
                        class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                </div>
                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">To</label>
                    <input type="date" name="to" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                </div>
            </div>
            <button class="w-full bg-ink text-paper text-sm font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors">
                Download Inquiry Report (CSV)
            </button>
        </form>
    </div>

    @if(session('admin_role') === 'primary')
        {{-- Commercial reports — primary admin only --}}
        <div class="bg-white border border-ink/15 rounded-sm p-6">
            <h2 class="font-display text-lg font-semibold text-ink mb-2">Plan Performance</h2>
            <p class="text-sm text-ink/70 leading-relaxed mb-5">
                Every plan with its specification, price, view count, inquiry count
                and view-to-inquiry conversion rate.
            </p>
            <a href="/admin/reports/plans.csv"
               class="block text-center w-full bg-ink text-paper text-sm font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors">
                Download Plan Performance (CSV)
            </a>
            <p class="font-mono text-xs uppercase tracking-wider text-clay mt-3">Primary admin only</p>
        </div>

        <div class="bg-white border border-ink/15 rounded-sm p-6">
            <h2 class="font-display text-lg font-semibold text-ink mb-2">Business Summary</h2>
            <p class="text-sm text-ink/70 leading-relaxed mb-5">
                Headline figures — total views, inquiries, conversion rates, quoted
                value versus converted value, and a breakdown by pipeline stage.
            </p>
            <a href="/admin/reports/summary.csv"
               class="block text-center w-full bg-ink text-paper text-sm font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors">
                Download Summary Report (CSV)
            </a>
            <p class="font-mono text-xs uppercase tracking-wider text-clay mt-3">Primary admin only</p>
        </div>
    @else
        <div class="bg-ink/5 border border-ink/15 rounded-sm p-6">
            <h2 class="font-display text-lg font-semibold text-ink mb-2">Commercial Reports</h2>
            <p class="text-sm text-ink/70 leading-relaxed">
                Plan performance and business summary reports contain commercial data
                and are available to the primary administrator only.
            </p>
        </div>
    @endif
</div>

@endsection
