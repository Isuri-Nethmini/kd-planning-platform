@extends('layouts.app')

@section('title', 'House Plans & Construction Services in Sri Lanka')

@section('content')

{{-- ───────────────────────── HERO ───────────────────────── --}}
@php
    // Hero background video slot.
    //
    // Drop the client's file in at public/media/hero.mp4 and it renders
    // automatically — no code change needed. Until then the blueprint grid
    // stands in on its own, which is the intended fallback.
    $heroVideo = file_exists(public_path('media/hero.mp4')) ? asset('media/hero.mp4') : null;
@endphp

<section class="bp-grid bg-ink text-paper relative overflow-hidden" data-tilt-area>
    @if($heroVideo)
        {{--
            Muted, looping background video. The poster frame shows first and
            remains visible if autoplay is blocked (common on iOS Low Power
            Mode), so the hero never falls back to an empty dark box.
            preload="none" on small screens keeps the 3 MB off mobile data.
        --}}
        <video
            class="absolute inset-0 w-full h-full object-cover opacity-35"
            poster="{{ asset('media/hero-poster.jpg') }}"
            autoplay muted loop playsinline preload="metadata"
            aria-hidden="true"
        >
            <source src="{{ $heroVideo }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-ink/70" aria-hidden="true"></div>
    @endif

    <div class="max-w-6xl mx-auto px-5 py-20 md:py-28 grid md:grid-cols-2 gap-12 items-center relative z-10">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-draft mb-4">KD Planning &amp; Design — Minuwangoda</p>
            <h1 class="font-display text-4xl md:text-5xl font-semibold leading-[1.1] mb-6">
                Find the house plan<br>you'll actually build.
            </h1>
            <p class="text-paper/75 text-lg leading-relaxed mb-8 max-w-md">
                Browse {{ $totalPlans > 0 ? $totalPlans.' ' : '' }}ready-made designs with full specifications — no login, no waiting on a phone call.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="/plans" class="btn-3d inline-flex items-center rounded-sm bg-draft text-ink font-medium px-6 py-3 hover:bg-draft/90">
                    Browse Plans
                </a>
                <a href="/inquire" class="inline-flex items-center rounded-sm border border-paper/30 text-paper font-medium px-6 py-3 hover:bg-paper/10 transition-colors">
                    Get an Estimate
                </a>
            </div>
        </div>

        {{--
            Isometric model of a floor plan. The same wall drawing is stacked
            twelve times, each layer raised a little further (--z), so at this
            angle the stack reads as solid walls on a floor slab. Pure CSS —
            see .iso-* in app.css. Decorative, so hidden from screen readers.
        --}}
        <div class="relative hidden sm:block" aria-hidden="true">
            <div class="iso-stage">
                <div class="iso-model" data-tilt="10">
                    <div class="iso-floor bp-grid"></div>

                    {{-- Floor annotations: door swing, dimension ticks --}}
                    <svg class="iso-layer" style="--z: 0px; opacity: .55" viewBox="0 0 300 210" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M150 62 A42 42 0 0 1 192 104" stroke-dasharray="3 4" />
                        <path d="M62 120 A38 38 0 0 1 100 158" stroke-dasharray="3 4" />
                        <line x1="10" y1="200" x2="290" y2="200" stroke-dasharray="1 6" />
                    </svg>

                    @for($i = 1; $i <= 12; $i++)
                        <svg class="iso-layer {{ $i === 12 ? 'is-top' : '' }}"
                             style="--z: {{ $i * 3 }}px; opacity: {{ $i === 12 ? 1 : 0.22 + $i * 0.045 }}"
                             viewBox="0 0 300 210" fill="none" stroke="currentColor"
                             stroke-width="{{ $i === 12 ? 3 : 4 }}" stroke-linecap="square">
                        <rect x="10" y="10" width="280" height="190" />
                        <line x1="150" y1="10" x2="150" y2="62" />
                        <line x1="150" y1="104" x2="150" y2="200" />
                        <line x1="150" y1="104" x2="290" y2="104" />
                        <line x1="10" y1="120" x2="62" y2="120" />
                        <line x1="100" y1="120" x2="150" y2="120" />
                        </svg>
                    @endfor
                </div>
            </div>
            <p class="text-center font-mono text-xs uppercase tracking-[0.2em] text-draft/80 -mt-6">
                Plan model · Scale 1:100
            </p>
        </div>
    </div>
</section>

{{-- ───────────────────────── TRUST STRIP ───────────────────────── --}}
<section class="border-b border-ink/10 bg-paper">
    <div class="max-w-6xl mx-auto px-5 py-5 flex flex-wrap gap-x-10 gap-y-2 justify-center text-sm font-mono text-ink/70">
        @if($totalPlans > 0)
            <span>{{ $totalPlans }} PLANS LISTED</span>
            <span class="text-clay/50">·</span>
        @endif
        <span>NEW PLANS WEEKLY</span>
        <span class="text-clay/50">·</span>
        <span>NO LOGIN TO INQUIRE</span>
        <span class="text-clay/50">·</span>
        <span>FREE ESTIMATES</span>
    </div>
</section>

{{-- ───────────────────────── FEATURED PLANS ───────────────────────── --}}
{{-- Each section below is skipped while it has nothing to show (e.g. a new site before content is added). --}}
@if($featuredPlans->isNotEmpty())
<section class="max-w-6xl mx-auto px-5 py-20">
    <div class="flex items-end justify-between mb-10">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-2">Featured Designs</p>
            <h2 class="font-display text-2xl md:text-3xl font-semibold text-ink">Popular plans this month</h2>
        </div>
        <a href="/plans" class="text-sm font-medium text-draft hover:underline hidden md:inline">View all plans →</a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($featuredPlans as $plan)
            <article data-tilt class="card-3d group bg-white border border-ink/10 rounded-sm overflow-hidden">
                <div class="relative aspect-[4/3] overflow-hidden bg-ink/5">
                    <x-image-frame
                        :src="$plan->primaryImage?->url"
                        :alt="$plan->name"
                    />
                    <span class="absolute top-3 left-3 font-mono text-xs bg-ink/80 text-paper px-2 py-1 rounded-sm">
                        PLAN NO. {{ str_pad($plan->id, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <div class="p-5">
                    <h3 class="font-display font-semibold text-lg text-ink mb-2">{{ $plan->name }}</h3>
                    <x-spec-readout :items="[$plan->bedrooms.' BR', $plan->bathrooms.' BA', number_format($plan->floor_area).' SQFT']" class="mb-3" />
                    <div class="flex items-center justify-end">
                        <a href="/plans/{{ $plan->id }}" class="text-sm font-medium text-draft hover:underline">View Plan →</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- ───────────────────────── HOW IT WORKS ───────────────────────── --}}
<x-how-it-works />

{{-- ───────────────────────── TESTIMONIALS ───────────────────────── --}}
@if($testimonials->isNotEmpty())
<section id="testimonials" class="bg-moss/10 py-20">
    <div class="max-w-6xl mx-auto px-5">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-moss mb-2 text-center">What clients say</p>
        <h2 class="font-display text-2xl md:text-3xl font-semibold text-ink text-center mb-12">Built on trust, not just blueprints</h2>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach ($testimonials as $t)
                <blockquote class="bg-white p-6 rounded-sm border border-ink/10">
                    <p class="text-ink/80 leading-relaxed mb-4">&ldquo;{{ $t->content }}&rdquo;</p>
                    <footer class="font-mono text-xs text-ink/70 uppercase tracking-wide">
                        {{ $t->client_name }} — {{ $t->location }}
                    </footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ───────────────────────── BLOG TEASER ───────────────────────── --}}
@if($blogPosts->isNotEmpty())
<section class="max-w-6xl mx-auto px-5 py-20">
    <div class="flex items-end justify-between mb-10">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-2">From the Blog</p>
            <h2 class="font-display text-2xl md:text-3xl font-semibold text-ink">Tips before you build</h2>
        </div>
    </div>
    <div class="grid md:grid-cols-3 gap-8">
        @foreach ($blogPosts as $post)
            <a href="/blog/{{ $post->slug }}" class="group block">
                <div class="aspect-[16/10] overflow-hidden rounded-sm bg-ink/5 mb-4">
                    <x-image-frame :src="$post->coverUrl" :alt="$post->title" note="Cover image pending" />
                </div>
                <h3 class="font-display font-semibold text-ink group-hover:text-draft transition-colors">{{ $post->title }}</h3>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ───────────────────────── CTA BANNER ───────────────────────── --}}
<section class="bg-ink text-paper">
    <div class="max-w-4xl mx-auto px-5 py-16 text-center">
        <h2 class="font-display text-2xl md:text-3xl font-semibold mb-4">Ready to start building?</h2>
        <p class="text-paper/75 mb-8 max-w-lg mx-auto">Send us your land details and the design you like, and we'll prepare a construction estimate — no obligation.</p>
        <a href="/inquire" class="inline-flex items-center rounded-sm bg-draft text-ink font-medium px-8 py-3 hover:bg-draft/90 transition-colors">
            Submit an Inquiry
        </a>
    </div>
</section>

@endsection
