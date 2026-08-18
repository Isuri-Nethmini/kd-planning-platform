@extends('layouts.app')

@section('title', 'Browse House Plans')

@section('content')

<div class="bg-ink text-paper py-12">
    <div class="max-w-6xl mx-auto px-5">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-draft mb-2">All Designs</p>
        <h1 class="font-display text-3xl md:text-4xl font-semibold">Browse House Plans</h1>
    </div>
</div>

<div class="max-w-6xl mx-auto px-5 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- ── FILTERS SIDEBAR ── --}}
        <aside class="w-full lg:w-56 shrink-0">
            <form method="GET" action="/plans" id="filter-form">
                <div class="bg-white border border-ink/10 rounded-sm p-5 space-y-6">

                    {{-- Search --}}
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-2">Search</label>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Plan name…"
                            class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft"
                        >
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-2">Category</label>
                        <div class="space-y-1">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="category" value="" {{ !request('category') ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-draft">
                                All categories
                            </label>
                            @foreach ($categories as $cat)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="category" value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'checked' : '' }} onchange="document.getElementById('filter-form').submit()" class="accent-draft">
                                    {{ $cat->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bedrooms --}}
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-2">Min Bedrooms</label>
                        <select name="bedrooms" onchange="document.getElementById('filter-form').submit()" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                            <option value="">Any</option>
                            @foreach ([1,2,3,4,5,6] as $n)
                                <option value="{{ $n }}" {{ request('bedrooms') == $n ? 'selected' : '' }}>{{ $n }}+</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Floors --}}
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-2">Floors</label>
                        <select name="floors" onchange="document.getElementById('filter-form').submit()" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                            <option value="">Any</option>
                            <option value="1" {{ request('floors') == 1 ? 'selected' : '' }}>Single Storey</option>
                            <option value="2" {{ request('floors') == 2 ? 'selected' : '' }}>Double Storey</option>
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-2">Sort By</label>
                        <select name="sort" onchange="document.getElementById('filter-form').submit()" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="most_viewed" {{ request('sort') === 'most_viewed' ? 'selected' : '' }}>Most Viewed</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Plan Price: Low → High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Plan Price: High → Low</option>
                        </select>
                    </div>

                    @if(request()->hasAny(['q','category','bedrooms','floors','sort']))
                        <a href="/plans" class="block text-center text-sm text-clay hover:underline">Clear filters</a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- ── PLAN GRID ── --}}
        <div class="flex-1">
            <div class="flex items-center justify-between mb-6">
                <p class="font-mono text-sm text-ink/50">{{ $plans->total() }} plan{{ $plans->total() !== 1 ? 's' : '' }} found</p>
            </div>

            @if($plans->isEmpty())
                <div class="text-center py-24 text-ink/40">
                    <p class="text-4xl mb-4">🏠</p>
                    <p class="font-display text-lg">No plans match your filters.</p>
                    <a href="/plans" class="text-sm text-draft hover:underline mt-2 inline-block">Clear and try again</a>
                </div>
            @else
                <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($plans as $plan)
                        <article class="group bg-white border border-ink/10 rounded-sm overflow-hidden hover:shadow-lg hover:shadow-ink/5 transition-shadow">
                            <div class="relative aspect-[4/3] overflow-hidden bg-ink/5">
                                <x-image-frame
                                    :src="$plan->primaryImage?->url"
                                    :alt="$plan->name"
                                    :label="'PLAN NO. '.str_pad($plan->id, 4, '0', STR_PAD_LEFT)"
                                />
                                <span class="absolute top-3 left-3 font-mono text-[11px] bg-ink/80 text-paper px-2 py-1 rounded-sm">
                                    PLAN NO. {{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <div class="p-5">
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($plan->categories->take(2) as $cat)
                                        <span class="font-mono text-[10px] uppercase tracking-wider bg-draft/10 text-draft px-2 py-0.5 rounded-sm">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                                <h2 class="font-display font-semibold text-ink mb-2 leading-snug">{{ $plan->name }}</h2>
                                <x-spec-readout :items="[$plan->bedrooms.' BR', $plan->bathrooms.' BA', $plan->floors.' FL', number_format($plan->floor_area).' SQFT']" class="mb-4" />
                                <a href="/plans/{{ $plan->id }}" class="block w-full text-center border border-ink/20 text-ink text-sm font-medium py-2 rounded-sm hover:bg-ink hover:text-paper transition-colors">
                                    View Plan
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($plans->hasPages())
                    <div class="mt-10 flex justify-center gap-2 font-mono text-sm">
                        @if($plans->onFirstPage())
                            <span class="px-3 py-2 text-ink/30">← Prev</span>
                        @else
                            <a href="{{ $plans->previousPageUrl() }}" class="px-3 py-2 border border-ink/20 rounded-sm hover:bg-ink hover:text-paper transition-colors">← Prev</a>
                        @endif

                        <span class="px-3 py-2 text-ink/60">Page {{ $plans->currentPage() }} of {{ $plans->lastPage() }}</span>

                        @if($plans->hasMorePages())
                            <a href="{{ $plans->nextPageUrl() }}" class="px-3 py-2 border border-ink/20 rounded-sm hover:bg-ink hover:text-paper transition-colors">Next →</a>
                        @else
                            <span class="px-3 py-2 text-ink/30">Next →</span>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

@endsection
