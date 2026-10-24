@extends('admin.layout')

@section('title', 'Payments')

@section('content')
<h1 class="font-display text-2xl font-semibold text-ink mb-1">Payments</h1>
<p class="text-sm text-ink/70 mb-6">Receipts uploaded by clients for offline bank transfers. Check each one against your bank statement before verifying.</p>

@if($errors->any())
    <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-3 rounded-sm mb-6">{{ $errors->first() }}</div>
@endif

<h2 class="font-display font-semibold text-ink mb-3">Awaiting review ({{ $pending->count() }})</h2>

@forelse($pending as $receipt)
    @php $instalment = $receipt->instalment; $project = $instalment->project; @endphp
    <div class="bg-white border border-draft/40 rounded-sm p-5 mb-4">
        <div class="grid md:grid-cols-3 gap-5">
            <div class="md:col-span-2 text-sm space-y-1">
                <p class="font-display font-semibold text-ink text-base">{{ $project->customer->name }} — {{ $instalment->label }}</p>
                <p class="text-ink/80"><a href="/admin/client-projects/{{ $project->id }}" class="hover:text-draft">{{ $project->title }}</a></p>
                <p><span class="text-ink/70">Due:</span> Rs. {{ number_format($instalment->amount) }}
                   · <span class="text-ink/70">Paid:</span>
                   <strong class="{{ (float) $receipt->amount_paid < (float) $instalment->amount ? 'text-clay' : 'text-ink' }}">Rs. {{ number_format($receipt->amount_paid) }}</strong>
                   @if((float) $receipt->amount_paid < (float) $instalment->amount)<span class="text-clay text-xs">(less than due)</span>@endif
                </p>
                <p><span class="text-ink/70">Paid on:</span> {{ $receipt->paid_on->format('d M Y') }} · <span class="text-ink/70">Reference:</span> {{ $receipt->reference ?: '—' }}</p>
                <p class="font-mono text-xs text-ink/70">Uploaded {{ $receipt->created_at->diffForHumans() }}</p>
                <a href="/files/receipts/{{ $receipt->id }}" target="_blank" rel="noopener"
                   class="inline-block mt-2 text-draft font-medium hover:underline">Open receipt ({{ $receipt->original_name }}) →</a>
            </div>
            <div class="space-y-3">
                <form method="POST" action="/admin/receipts/{{ $receipt->id }}/verify"
                      onsubmit="return confirm('Confirm this payment appears in the bank account?')">
                    @csrf
                    <button class="w-full bg-moss text-white text-sm font-medium py-2.5 rounded-sm hover:bg-moss/90">Verify payment</button>
                </form>
                <form method="POST" action="/admin/receipts/{{ $receipt->id }}/reject" class="space-y-2">
                    @csrf
                    <input type="text" name="rejection_reason" required maxlength="255" placeholder="Reason (the client sees this)"
                        class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-clay">
                    <button class="w-full border border-clay/50 text-clay text-sm font-medium py-2 rounded-sm hover:bg-clay hover:text-white">Reject</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="bg-white border border-ink/15 rounded-sm p-8 text-center text-ink/70 mb-4">Nothing waiting. All receipts have been reviewed.</div>
@endforelse

<h2 class="font-display font-semibold text-ink mt-10 mb-3">Recently reviewed</h2>
<div class="bg-white border border-ink/15 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/15">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Client / instalment</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Amount</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70">Decision</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/70 hidden md:table-cell">By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent as $receipt)
                <tr class="border-b border-ink/10 last:border-0">
                    <td class="px-4 py-3">{{ $receipt->instalment->project->customer->name }} — {{ $receipt->instalment->label }}</td>
                    <td class="px-4 py-3 font-mono text-xs">Rs. {{ number_format($receipt->amount_paid) }}</td>
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs uppercase px-2 py-1 rounded-sm {{ $receipt->status === 'verified' ? 'bg-moss/15 text-moss' : 'bg-clay/15 text-clay' }}">{{ $receipt->status }}</span>
                        @if($receipt->rejection_reason)<p class="text-xs text-ink/70 mt-1">{{ $receipt->rejection_reason }}</p>@endif
                    </td>
                    <td class="px-4 py-3 text-xs text-ink/70 hidden md:table-cell">{{ $receipt->reviewer?->name ?? '—' }}<br>{{ $receipt->reviewed_at?->format('d M Y, H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-ink/70">No reviewed receipts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
