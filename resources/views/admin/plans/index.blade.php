@extends('admin.layout')

@section('title', 'House Plans')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">House Plans</h1>
    <a href="/admin/plans/create" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">
        + Add New Plan
    </a>
</div>

<div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/10">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Plan</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden md:table-cell">Specs</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden lg:table-cell">Price</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Status</th>
                <th class="text-right px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plans as $plan)
                <tr class="border-b border-ink/5 last:border-0 hover:bg-ink/2 transition-colors">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-10 rounded-sm overflow-hidden bg-ink/5 shrink-0">
                                <x-image-frame :src="$plan->primaryImage?->url" :alt="$plan->name" :zoom="false" note="" />
                            </div>
                            <div>
                                <p class="font-medium text-ink">{{ $plan->name }}</p>
                                <p class="font-mono text-xs text-ink/40">
                                    PLAN-{{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}
                                    @if($plan->is_featured)
                                        &nbsp;⭐
                                    @endif
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-ink/60 hidden md:table-cell">
                        {{ $plan->bedrooms }}BR · {{ $plan->bathrooms }}BA · {{ $plan->floors }}FL
                    </td>
                    <td class="px-4 py-4 font-mono text-clay hidden lg:table-cell">
                        Rs. {{ number_format($plan->price) }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm
                            {{ $plan->is_active ? 'bg-moss/10 text-moss' : 'bg-ink/5 text-ink/40' }}">
                            {{ $plan->is_active ? 'Active' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="/plans/{{ $plan->id }}" target="_blank" class="text-xs text-draft hover:underline">View</a>
                            <a href="/admin/plans/{{ $plan->id }}/edit" class="text-xs text-ink hover:underline">Edit</a>
                            <form method="POST" action="/admin/plans/{{ $plan->id }}" onsubmit="return confirm('Delete this plan?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-clay hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-ink/40">No plans yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($plans->hasPages())
    <div class="mt-6 flex justify-center gap-2 font-mono text-sm">
        @if(!$plans->onFirstPage())
            <a href="{{ $plans->previousPageUrl() }}" class="px-3 py-2 border border-ink/20 rounded-sm hover:bg-ink hover:text-paper transition-colors">← Prev</a>
        @endif
        <span class="px-3 py-2 text-ink/60">Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</span>
        @if($plans->hasMorePages())
            <a href="{{ $plans->nextPageUrl() }}" class="px-3 py-2 border border-ink/20 rounded-sm hover:bg-ink hover:text-paper transition-colors">Next →</a>
        @endif
    </div>
@endif

@endsection
