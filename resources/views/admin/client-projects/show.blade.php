@extends('admin.layout')

@section('title', $project->title)

@section('content')
<div class="flex flex-wrap items-center gap-3 mb-2">
    <a href="/admin/clients/{{ $project->customer->id }}" class="text-sm text-ink/70 hover:text-draft">← {{ $project->customer->name }}</a>
    <span class="text-ink/30">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">{{ $project->title }}</h1>
</div>
<div class="flex items-center gap-3 mb-6">
    <div class="w-48 h-2 bg-ink/10 rounded-sm overflow-hidden"><div class="h-full bg-draft" style="width: {{ $project->progress }}%"></div></div>
    <span class="font-mono text-xs text-ink/70">{{ $project->progress }}% complete · what the client sees in their portal</span>
</div>

@if($errors->any())
    <div class="bg-clay/10 border border-clay/40 text-clay text-sm px-4 py-4 rounded-sm mb-6">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

{{-- ── Project details ──────────────────────────────────────────── --}}
<details class="bg-white border border-ink/15 rounded-sm p-5 mb-8">
    <summary class="cursor-pointer font-display font-semibold text-ink">Project details</summary>
    <form method="POST" action="/admin/client-projects/{{ $project->id }}" class="mt-5 space-y-5">
        @csrf @method('PUT')
        @include('admin.clients._project-fields', ['project' => $project])
        <div class="max-w-xs">
            <label class="font-mono text-xs uppercase tracking-wider text-ink/70 block mb-1">Project status</label>
            <select name="status" class="w-full border border-ink/25 rounded-sm px-3 py-2.5 text-sm">
                @foreach(\App\Models\ClientProject::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected($project->status === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="bg-ink text-paper text-sm font-medium px-6 py-2.5 rounded-sm">Save details</button>
    </form>
</details>

<div class="grid xl:grid-cols-5 gap-8">

    {{-- ── Construction stages ──────────────────────────────── --}}
    <section class="xl:col-span-3">
        <h2 class="font-display text-lg font-semibold text-ink mb-1">Construction stages</h2>
        <p class="text-sm text-ink/70 mb-4">Changing a stage to <em>In progress</em> or <em>Completed</em> emails the client. Notes and photos appear in their portal.</p>

        @foreach($project->stages as $stage)
            <div class="bg-white border rounded-sm p-5 mb-4 {{ $stage->status === 'in_progress' ? 'border-draft' : 'border-ink/15' }}">
                <form method="POST" action="/admin/client-projects/{{ $project->id }}/stages/{{ $stage->id }}" class="space-y-3">
                    @csrf @method('PUT')
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-mono text-xs text-ink/60">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <input type="text" name="name" value="{{ $stage->name }}" required
                            class="flex-1 min-w-40 border border-ink/20 rounded-sm px-3 py-1.5 text-sm font-medium focus:outline-none focus:border-draft">
                        <select name="status" class="border border-ink/20 rounded-sm px-2 py-1.5 text-sm">
                            @foreach(\App\Models\ProjectStage::STATUSES as $value => $label)
                                <option value="{{ $value }}" @selected($stage->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <label class="text-xs text-ink/70">Started
                            <input type="date" name="started_on" value="{{ $stage->started_on?->toDateString() }}" class="block w-full border border-ink/20 rounded-sm px-2 py-1.5 text-sm mt-1">
                        </label>
                        <label class="text-xs text-ink/70">Completed
                            <input type="date" name="completed_on" value="{{ $stage->completed_on?->toDateString() }}" class="block w-full border border-ink/20 rounded-sm px-2 py-1.5 text-sm mt-1">
                        </label>
                    </div>
                    <textarea name="note" rows="2" maxlength="1000" placeholder="Update for the client (optional)"
                        class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">{{ $stage->note }}</textarea>
                    <button class="bg-ink text-paper text-xs font-medium px-4 py-2 rounded-sm">Save stage</button>
                </form>

                {{-- Photos --}}
                <div class="mt-4 pt-4 border-t border-ink/10">
                    @if($stage->photos->isNotEmpty())
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 mb-3">
                            @foreach($stage->photos as $photo)
                                <div class="relative group">
                                    <a href="/files/photos/{{ $photo->id }}" target="_blank" rel="noopener" class="block aspect-square overflow-hidden rounded-sm bg-ink/5">
                                        <img src="/files/photos/{{ $photo->id }}" alt="{{ $photo->caption ?? $stage->name }}" class="w-full h-full object-cover" loading="lazy">
                                    </a>
                                    <form method="POST" action="/admin/stage-photos/{{ $photo->id }}" onsubmit="return confirm('Remove this photo?')"
                                          class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 focus-within:opacity-100">
                                        @csrf @method('DELETE')
                                        <button class="bg-clay text-white text-xs w-6 h-6 rounded-sm" aria-label="Remove photo">×</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <form method="POST" action="/admin/client-projects/{{ $project->id }}/stages/{{ $stage->id }}/photos"
                          enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                        @csrf
                        <input type="file" name="photos[]" multiple accept="image/*" required
                            class="text-xs text-ink/80 file:mr-2 file:py-1.5 file:px-3 file:rounded-sm file:border file:border-ink/25 file:bg-paper file:text-xs">
                        <input type="text" name="caption" placeholder="Caption (optional)" maxlength="255"
                            class="flex-1 min-w-32 border border-ink/20 rounded-sm px-2 py-1.5 text-xs">
                        <button class="border border-ink/30 text-xs font-medium px-3 py-1.5 rounded-sm hover:bg-ink hover:text-paper">Add photos</button>
                    </form>
                </div>
            </div>
        @endforeach

        <form method="POST" action="/admin/client-projects/{{ $project->id }}/stages" class="flex gap-2">
            @csrf
            <input type="text" name="name" required maxlength="255" placeholder="New stage name, e.g. Garden wall"
                class="flex-1 border border-ink/25 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
            <button class="border border-ink/30 text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink hover:text-paper">Add stage</button>
        </form>
    </section>

    {{-- ── Payments (primary admins only) ───────────────────────── --}}
    <section class="xl:col-span-2">
        <h2 class="font-display text-lg font-semibold text-ink mb-1">Payment schedule</h2>

        @if(! $isPrimary)
            <div class="bg-white border border-ink/15 rounded-sm p-5 text-sm text-ink/75">
                Payments are managed by the primary admin.
            </div>
        @else
            <p class="text-sm text-ink/70 mb-4">
                Scheduled Rs. {{ number_format($project->scheduled_total) }}
                @if($project->contract_value) of Rs. {{ number_format($project->contract_value) }} contract @endif
                · Verified Rs. {{ number_format($project->paid_total) }}
            </p>

            @forelse($project->instalments as $instalment)
                <div class="bg-white border border-ink/15 rounded-sm p-4 mb-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-medium text-ink">{{ $instalment->label }}</p>
                            <p class="text-sm text-ink/80">Rs. {{ number_format($instalment->amount) }}
                                @if($instalment->due_date)<span class="font-mono text-xs {{ $instalment->is_overdue ? 'text-clay' : 'text-ink/70' }}"> · due {{ $instalment->due_date->format('d M Y') }}</span>@endif
                            </p>
                        </div>
                        <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm whitespace-nowrap {{ $instalment->status_class }}">{{ $instalment->status_label }}</span>
                    </div>

                    @if($instalment->receipts->isNotEmpty())
                        <ul class="mt-3 space-y-1 text-xs">
                            @foreach($instalment->receipts as $receipt)
                                <li class="flex justify-between gap-2">
                                    <a href="/files/receipts/{{ $receipt->id }}" target="_blank" rel="noopener" class="text-draft hover:underline truncate">{{ $receipt->original_name }}</a>
                                    <span class="font-mono uppercase text-ink/70 whitespace-nowrap">
                                        {{ $receipt->status }}@if($receipt->reviewer) · {{ $receipt->reviewer->name }}@endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($instalment->status === 'submitted')
                        <a href="/admin/payments" class="inline-block mt-3 text-xs font-medium text-draft hover:underline">Review in Payments →</a>
                    @elseif($instalment->receipts->isEmpty())
                        <form method="POST" action="/admin/instalments/{{ $instalment->id }}" onsubmit="return confirm('Remove this instalment?')" class="mt-2">
                            @csrf @method('DELETE')
                            <button class="text-xs text-clay hover:underline">Remove</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-dashed border-ink/25 rounded-sm p-5 text-sm text-ink/75 mb-3">
                    No instalments yet. Add the payment schedule you agreed with the client.
                </div>
            @endforelse

            <form method="POST" action="/admin/client-projects/{{ $project->id }}/instalments" class="bg-white border border-ink/15 rounded-sm p-4 space-y-3">
                @csrf
                <p class="font-mono text-xs uppercase tracking-wider text-ink/70">Add instalment</p>
                <input type="text" name="label" required maxlength="100" placeholder="e.g. Advance, Foundation, Final"
                    class="w-full border border-ink/25 rounded-sm px-3 py-2 text-sm">
                <div class="grid grid-cols-2 gap-2">
                    <input type="number" name="amount" step="0.01" min="1" required placeholder="Amount (Rs.)"
                        class="border border-ink/25 rounded-sm px-3 py-2 text-sm">
                    <input type="date" name="due_date" class="border border-ink/25 rounded-sm px-3 py-2 text-sm" aria-label="Due date">
                </div>
                <button class="bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm">Add</button>
            </form>
        @endif
    </section>
</div>
@endsection
