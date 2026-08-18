@extends('admin.layout')

@section('title', 'Inquiries')

@section('content')

<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h1 class="font-display text-2xl font-semibold text-ink">Inquiries</h1>
    <form method="GET" action="/admin/inquiries" class="flex gap-2">
        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Name, email or phone…"
            class="border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
        <button class="bg-ink text-paper text-sm px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">Search</button>
    </form>
</div>

{{-- Status tabs --}}
<div class="flex flex-wrap gap-2 mb-6">
    @foreach(['all' => 'All'] + \App\Models\Inquiry::STATUSES as $key => $label)
        @php $isActive = request('status', 'all') === $key; @endphp
        <a href="/admin/inquiries?status={{ $key }}{{ request('q') ? '&q='.urlencode(request('q')) : '' }}"
           class="font-mono text-xs uppercase tracking-wider px-3 py-2 rounded-sm border transition-colors
                  {{ $isActive ? 'bg-ink text-paper border-ink' : 'border-ink/20 text-ink/60 hover:border-draft hover:text-draft' }}">
            {{ $label }} ({{ $counts[$key] }})
        </a>
    @endforeach
</div>

<div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/10">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">From</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden md:table-cell">Plan</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden lg:table-cell">Received</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden xl:table-cell">Quoted</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Status</th>
                <th class="text-right px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inquiries as $inquiry)
                <tr class="border-b border-ink/5 last:border-0 hover:bg-ink/5 transition-colors">
                    <td class="px-4 py-4">
                        <p class="font-medium text-ink">{{ $inquiry->name }}</p>
                        <p class="font-mono text-xs text-ink/40">{{ $inquiry->email }}</p>
                    </td>
                    <td class="px-4 py-4 text-ink/60 hidden md:table-cell">
                        {{ $inquiry->housePlan?->name ?? 'General inquiry' }}
                    </td>
                    <td class="px-4 py-4 font-mono text-xs text-ink/40 hidden lg:table-cell">
                        {{ $inquiry->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-4 py-4 font-mono text-xs hidden xl:table-cell {{ $inquiry->quoted_amount ? 'text-clay' : 'text-ink/25' }}">
                        {{ $inquiry->quoted_amount ? 'Rs. '.number_format($inquiry->quoted_amount) : '—' }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm {{ $inquiry->status_class }}">
                            {{ $inquiry->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <a href="/admin/inquiries/{{ $inquiry->id }}" class="text-xs text-draft hover:underline">Open</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-ink/40">No inquiries found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($inquiries->hasPages())
    <div class="mt-6">{{ $inquiries->links() }}</div>
@endif

@endsection
