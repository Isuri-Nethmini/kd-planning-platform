@extends('admin.layout')

@section('title', 'Analytics')

@section('content')

<div class="mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">Analytics &amp; Reports</h1>
    <p class="text-ink/50 text-sm mt-1">How the catalogue is performing and where inquiries are coming from.</p>
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
    @foreach([
        ['Total Plans',      number_format($summary['total_plans'])],
        ['Active Plans',     number_format($summary['active_plans'])],
        ['Total Plan Views', number_format($summary['total_views'])],
        ['Total Inquiries',  number_format($summary['total_inquiries'])],
        ['View → Inquiry',   $summary['conversion'].'%'],
    ] as [$label, $value])
        <div class="bg-white border border-ink/10 rounded-sm p-5">
            <p class="font-mono text-xs uppercase tracking-wider text-ink/40 mb-2">{{ $label }}</p>
            <p class="font-display text-2xl font-bold text-ink">{{ $value }}</p>
        </div>
    @endforeach
</div>

{{-- Inquiries over time --}}
<div class="bg-white border border-ink/10 rounded-sm p-6 mb-8">
    <h2 class="font-display font-semibold text-ink mb-6">Inquiries — Last 6 Months</h2>
    <div class="flex items-end justify-between gap-3 h-48">
        @foreach($months as $month)
            <div class="flex-1 flex flex-col items-center justify-end h-full">
                <span class="font-mono text-xs text-ink/50 mb-1">{{ $month['count'] }}</span>
                <div class="w-full bg-draft/80 rounded-t-sm transition-all hover:bg-draft"
                     style="height: {{ max(2, round(($month['count'] / $peakMonth) * 100)) }}%"
                     title="{{ $month['label'] }}: {{ $month['count'] }} inquiries"></div>
                <span class="font-mono text-[10px] uppercase tracking-wider text-ink/40 mt-2 text-center">
                    {{ $month['label'] }}
                </span>
            </div>
        @endforeach
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8 mb-8">

    {{-- Most viewed --}}
    <div>
        <h2 class="font-display font-semibold text-ink mb-4">Most Viewed Plans</h2>
        <div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
            @forelse($topViewed as $i => $plan)
                <div class="flex items-center gap-3 px-4 py-3 border-b border-ink/5 last:border-0">
                    <span class="font-mono text-xs text-ink/30 w-5">{{ $i + 1 }}</span>
                    <p class="flex-1 text-sm font-medium text-ink truncate">{{ $plan->name }}</p>
                    <span class="font-mono text-xs text-draft">{{ number_format($plan->view_count) }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-ink/40 text-sm">No data yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Most inquired --}}
    <div>
        <h2 class="font-display font-semibold text-ink mb-4">Most Inquired Plans</h2>
        <div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
            @forelse($topInquired as $i => $plan)
                <div class="flex items-center gap-3 px-4 py-3 border-b border-ink/5 last:border-0">
                    <span class="font-mono text-xs text-ink/30 w-5">{{ $i + 1 }}</span>
                    <p class="flex-1 text-sm font-medium text-ink truncate">{{ $plan->name }}</p>
                    <span class="font-mono text-xs text-clay">{{ $plan->inquiries_count }}</span>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-ink/40 text-sm">No inquiries linked to a plan yet.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">

    {{-- Categories --}}
    <div>
        <h2 class="font-display font-semibold text-ink mb-4">Plans per Category</h2>
        <div class="bg-white border border-ink/10 rounded-sm p-5 space-y-3">
            @php $maxCat = max(1, $categoryBreakdown->max('house_plans_count')); @endphp
            @foreach($categoryBreakdown as $cat)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-ink/70">{{ $cat->name }}</span>
                        <span class="font-mono text-xs text-ink/40">{{ $cat->house_plans_count }}</span>
                    </div>
                    <div class="h-2 bg-ink/5 rounded-sm overflow-hidden">
                        <div class="h-full bg-moss/70" style="width: {{ round(($cat->house_plans_count / $maxCat) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Inquiry status --}}
    <div>
        <h2 class="font-display font-semibold text-ink mb-4">Inquiry Status</h2>
        <div class="bg-white border border-ink/10 rounded-sm p-5 space-y-3">
            @php $totalStatus = max(1, array_sum($statusBreakdown)); @endphp
            @foreach(['new' => 'clay', 'read' => 'draft', 'responded' => 'moss'] as $status => $color)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-ink/70 capitalize">{{ $status }}</span>
                        <span class="font-mono text-xs text-ink/40">
                            {{ $statusBreakdown[$status] }}
                            ({{ round(($statusBreakdown[$status] / $totalStatus) * 100) }}%)
                        </span>
                    </div>
                    <div class="h-2 bg-ink/5 rounded-sm overflow-hidden">
                        <div class="h-full bg-{{ $color }}/70" style="width: {{ round(($statusBreakdown[$status] / $totalStatus) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
