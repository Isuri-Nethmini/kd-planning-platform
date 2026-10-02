@extends('portal.layout')

@section('title', $project->title)

@section('portal')

{{-- ── Project summary ─────────────────────────────────────────── --}}
<div class="bg-white border border-ink/15 rounded-sm p-6 mb-8">
    <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.18em] text-clay mb-1">{{ $project->status_label }}</p>
            <h1 class="font-display text-2xl md:text-3xl font-semibold text-ink">{{ $project->title }}</h1>
            <p class="text-ink/75 mt-1">
                {{ $project->site_location }}
                @if($project->housePlan) · Based on <a href="/plans/{{ $project->housePlan->id }}" class="text-draft hover:underline">{{ $project->housePlan->name }}</a> @endif
            </p>
        </div>
        <div class="text-right">
            <p class="font-display text-4xl font-bold text-draft">{{ $project->progress }}%</p>
            <p class="font-mono text-xs uppercase tracking-wider text-ink/70">complete</p>
        </div>
    </div>

    <div class="h-3 bg-ink/10 rounded-sm overflow-hidden mb-5" role="progressbar"
         aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
        <div class="h-full bg-draft" style="width: {{ $project->progress }}%"></div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="font-mono text-xs uppercase tracking-wider text-ink/70">Current stage</p>
            <p class="text-ink font-medium">{{ $project->current_stage?->name ?? 'All stages complete' }}</p>
        </div>
        <div>
            <p class="font-mono text-xs uppercase tracking-wider text-ink/70">Started</p>
            <p class="text-ink font-medium">{{ $project->start_date?->format('d M Y') ?? 'Not yet' }}</p>
        </div>
        <div>
            <p class="font-mono text-xs uppercase tracking-wider text-ink/70">Expected completion</p>
            <p class="text-ink font-medium">{{ $project->estimated_completion?->format('d M Y') ?? 'To be confirmed' }}</p>
        </div>
        <div>
            <p class="font-mono text-xs uppercase tracking-wider text-ink/70">Paid so far</p>
            <p class="text-ink font-medium">
                Rs. {{ number_format($project->paid_total) }}
                @if($project->scheduled_total > 0)
                    <span class="text-ink/70 font-normal">of {{ number_format($project->scheduled_total) }}</span>
                @endif
            </p>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-5 gap-8">

    {{-- ── Construction timeline ──────────────────────────────── --}}
    <section class="lg:col-span-3">
        <h2 class="font-display text-xl font-semibold text-ink mb-4">Construction progress</h2>

        <ol class="space-y-4">
            @foreach($project->stages as $stage)
                <li class="bg-white border rounded-sm p-5 {{ $stage->status === 'in_progress' ? 'border-draft' : 'border-ink/15' }}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-display font-semibold text-ink">
                            <span class="font-mono text-xs text-ink/60 mr-2">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            {{ $stage->name }}
                        </h3>
                        <span class="font-mono text-xs uppercase px-2 py-1 rounded-sm {{ $stage->status_class }}">{{ $stage->status_label }}</span>
                    </div>

                    @if($stage->started_on || $stage->completed_on)
                        <p class="font-mono text-xs text-ink/70 mt-2">
                            @if($stage->started_on) Started {{ $stage->started_on->format('d M Y') }} @endif
                            @if($stage->completed_on) · Completed {{ $stage->completed_on->format('d M Y') }} @endif
                        </p>
                    @endif

                    @if($stage->note)
                        <p class="text-sm text-ink/80 mt-3 leading-relaxed">{{ $stage->note }}</p>
                    @endif

                    @if($stage->photos->isNotEmpty())
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 mt-4">
                            @foreach($stage->photos as $photo)
                                <a href="/files/photos/{{ $photo->id }}" target="_blank" rel="noopener"
                                   class="block aspect-square overflow-hidden rounded-sm bg-ink/5"
                                   title="{{ $photo->caption ?? $stage->name }}">
                                    <img src="/files/photos/{{ $photo->id }}" alt="{{ $photo->caption ?? 'Site photo — ' . $stage->name }}"
                                         loading="lazy" class="w-full h-full object-cover hover:opacity-90 transition-opacity">
                                </a>
                            @endforeach
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    </section>

    {{-- ── Payments ───────────────────────────────────────────── --}}
    <section class="lg:col-span-2">
        <h2 class="font-display text-xl font-semibold text-ink mb-2">Payments</h2>
        <p class="text-sm text-ink/75 mb-4 leading-relaxed">
            Pay by bank transfer, then upload your receipt here. We'll verify it and email you.
        </p>

        @if($errors->any())
            <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-3 rounded-sm mb-4">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @forelse($project->instalments as $instalment)
            <div class="bg-white border rounded-sm p-5 mb-4 {{ $instalment->is_overdue ? 'border-clay' : 'border-ink/15' }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-display font-semibold text-ink">{{ $instalment->label }}</p>
                        <p class="text-sm text-ink/80">Rs. {{ number_format($instalment->amount) }}</p>
                        @if($instalment->due_date)
                            <p class="font-mono text-xs mt-1 {{ $instalment->is_overdue ? 'text-clay' : 'text-ink/70' }}">
                                {{ $instalment->is_overdue ? 'Overdue since' : 'Due' }} {{ $instalment->due_date->format('d M Y') }}
                            </p>
                        @endif
                    </div>
                    <span class="font-mono text-xs uppercase px-2 py-1 rounded-sm whitespace-nowrap {{ $instalment->status_class }}">
                        {{ $instalment->status_label }}
                    </span>
                </div>

                @if($instalment->status === 'rejected')
                    <p class="text-sm text-clay mt-3">
                        <strong>Reason:</strong> {{ $instalment->latestReceipt->rejection_reason }}
                    </p>
                @endif

                @if($instalment->can_upload)
                    <details class="mt-4" {{ $errors->any() && old('instalment_id') == $instalment->id ? 'open' : '' }}>
                        <summary class="cursor-pointer text-sm font-medium text-draft hover:underline">
                            {{ $instalment->status === 'rejected' ? 'Upload a new receipt' : 'Upload receipt' }}
                        </summary>
                        <form method="POST" action="/portal/instalments/{{ $instalment->id }}/receipts"
                              enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="instalment_id" value="{{ $instalment->id }}">
                            <div>
                                <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Receipt (JPG, PNG or PDF, max 5 MB)</label>
                                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.webp,.pdf" required
                                    class="w-full text-sm text-ink/80 file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border file:border-ink/25 file:bg-paper file:text-xs">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Amount paid (Rs.)</label>
                                    <input type="number" name="amount_paid" step="0.01" min="1" required
                                        value="{{ old('amount_paid', (int) $instalment->amount) }}"
                                        class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                                </div>
                                <div>
                                    <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Date paid</label>
                                    <input type="date" name="paid_on" required max="{{ today()->toDateString() }}"
                                        value="{{ old('paid_on', today()->toDateString()) }}"
                                        class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                                </div>
                            </div>
                            <div>
                                <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Bank reference (optional)</label>
                                <input type="text" name="reference" maxlength="100" value="{{ old('reference') }}"
                                    class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                            </div>
                            <button class="bg-ink text-paper text-sm font-medium px-5 py-2.5 rounded-sm hover:bg-ink/90 transition-colors">
                                Submit receipt
                            </button>
                        </form>
                    </details>
                @endif

                @if($instalment->receipts->isNotEmpty())
                    <ul class="mt-4 pt-4 border-t border-ink/10 space-y-1.5">
                        @foreach($instalment->receipts as $receipt)
                            <li class="flex items-center justify-between gap-3 text-xs">
                                <a href="/files/receipts/{{ $receipt->id }}" target="_blank" rel="noopener" class="text-draft hover:underline truncate">
                                    {{ $receipt->original_name }}
                                </a>
                                <span class="font-mono uppercase text-ink/70 whitespace-nowrap">
                                    {{ $receipt->status }} · {{ $receipt->created_at->format('d M') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @empty
            <div class="bg-white border border-ink/15 rounded-sm p-6 text-sm text-ink/75">
                Your payment schedule hasn't been added yet.
            </div>
        @endforelse
    </section>
</div>
@endsection
