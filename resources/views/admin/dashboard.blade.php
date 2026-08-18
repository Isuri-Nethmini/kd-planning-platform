@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="font-display text-2xl font-semibold text-ink">Dashboard</h1>
        <p class="text-ink/50 text-sm mt-1">Welcome back, {{ session('admin_name') }}</p>
    </div>
    <a href="/admin/plans/create" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">
        + Add New Plan
    </a>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-10">
    @foreach([
        ['Total Plans',      $stats['total_plans'],      'ink'],
        ['Active Plans',     $stats['active_plans'],     'moss'],
        ['All Inquiries',    $stats['total_inquiries'],  'draft'],
        ['New Inquiries',    $stats['new_inquiries'],    'clay'],
        ['This Week',        $stats['this_week'],        'draft'],
        ['Converted',        $stats['converted'],        'moss'],
    ] as [$label, $value, $color])
        <div class="bg-white border border-ink/10 rounded-sm p-5">
            <p class="font-mono text-xs uppercase tracking-wider text-ink/40 mb-2">{{ $label }}</p>
            <p class="font-display text-3xl font-bold text-{{ $color }}">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-8">

    {{-- Recent Inquiries --}}
    <div class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-display font-semibold text-ink">Recent Inquiries</h2>
            <a href="/admin/inquiries" class="text-sm text-draft hover:underline">View all →</a>
        </div>
        <div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
            @forelse($recentInquiries as $inquiry)
                <div class="flex items-center justify-between px-5 py-4 border-b border-ink/5 last:border-0">
                    <div>
                        <p class="font-medium text-ink text-sm">{{ $inquiry->name }}</p>
                        <p class="text-ink/50 text-xs">
                            {{ $inquiry->housePlan?->name ?? 'General inquiry' }}
                            &nbsp;·&nbsp;
                            {{ $inquiry->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm {{ $inquiry->status_class }}">
                        {{ $inquiry->status_label }}
                    </span>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-ink/40 text-sm">No inquiries yet.</div>
            @endforelse
        </div>
    </div>

    {{-- Most Viewed Plans --}}
    <div>
        <h2 class="font-display font-semibold text-ink mb-4">Most Viewed Plans</h2>
        <div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
            @foreach($mostViewed as $i => $plan)
                <div class="flex items-center gap-3 px-4 py-3 border-b border-ink/5 last:border-0">
                    <span class="font-mono text-xs text-ink/30 w-4">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-ink truncate">{{ $plan->name }}</p>
                        <p class="font-mono text-xs text-ink/40">{{ number_format($plan->view_count) }} views</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
