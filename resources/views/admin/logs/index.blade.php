@extends('admin.layout')

@section('title', 'Login Activity')

@section('content')

<div class="mb-2">
    <h1 class="font-display text-2xl font-semibold text-ink">Login Activity</h1>
    <p class="text-ink/70 text-sm mt-1">
        Every sign-in attempt, successful or failed. Repeated failures against one account
        are what a brute-force attempt looks like.
    </p>
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 my-6">
    @foreach([
        ['Total attempts', $stats['total'], 'text-ink'],
        ['Successful', $stats['successful'], 'text-moss'],
        ['Failed', $stats['failed'], 'text-clay'],
        ['Failed (last 24h)', $stats['failed_24h'], 'text-clay'],
    ] as [$label, $value, $colour])
        <div class="bg-white border border-ink/15 rounded-sm p-4">
            <p class="font-mono text-xs uppercase tracking-wider text-ink/70 mb-1">{{ $label }}</p>
            <p class="font-display text-2xl font-bold {{ $colour }}">{{ number_format($value) }}</p>
        </div>
    @endforeach
</div>

{{-- Filters --}}
<div class="flex flex-wrap items-center gap-2 mb-5">
    @foreach(['all' => 'All', 'success' => 'Successful', 'failed' => 'Failed'] as $key => $label)
        @php $isActive = request('result', 'all') === $key; @endphp
        <a href="/admin/logs?result={{ $key }}"
           class="font-mono text-xs uppercase tracking-wider px-3 py-2 rounded-sm border transition-colors
                  {{ $isActive ? 'bg-ink text-paper border-ink' : 'border-ink/25 text-ink/70 hover:border-draft hover:text-draft' }}">
            {{ $label }}
        </a>
    @endforeach

    <form method="GET" action="/admin/logs" class="flex gap-2 ml-auto">
        <input type="hidden" name="result" value="{{ request('result', 'all') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Email or IP address…"
            class="border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
        <button class="bg-ink text-paper text-sm px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">Search</button>
    </form>
</div>

<div class="bg-white border border-ink/15 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/15">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">When</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Account</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Result</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70 hidden md:table-cell">IP Address</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70 hidden lg:table-cell">Browser</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr class="border-b border-ink/10 last:border-0">
                    <td class="px-4 py-3 font-mono text-xs text-ink/70 whitespace-nowrap">
                        {{ $log->attempted_at->format('d M Y, H:i:s') }}
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-ink">{{ $log->admin?->name ?? 'Unknown account' }}</p>
                        <p class="font-mono text-xs text-ink/70">{{ $log->email }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs uppercase px-2 py-1 rounded-sm
                            {{ $log->successful ? 'bg-moss/15 text-moss' : 'bg-clay/15 text-clay' }}">
                            {{ $log->successful ? 'Success' : 'Failed' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-ink/70 hidden md:table-cell">{{ $log->ip_address ?? '—' }}</td>
                    <td class="px-4 py-3 text-ink/70 hidden lg:table-cell">{{ $log->browser }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-ink/70">No login activity recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($logs->hasPages())
    <div class="mt-6">{{ $logs->links() }}</div>
@endif

<p class="text-xs text-ink/70 mt-4">
    Accounts lock for 15 minutes after 5 failed attempts from the same email and IP address.
</p>

@endsection
