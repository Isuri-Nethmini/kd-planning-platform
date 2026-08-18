@extends('layouts.app')

@section('title', $housePlan->name)

@section('content')

<div class="max-w-6xl mx-auto px-5 py-10">

    {{-- Breadcrumb --}}
    <nav class="font-mono text-xs text-ink/50 mb-8">
        <a href="/" class="hover:text-draft">Home</a>
        <span class="mx-2">/</span>
        <a href="/plans" class="hover:text-draft">Plans</a>
        <span class="mx-2">/</span>
        <span class="text-ink">{{ $housePlan->name }}</span>
    </nav>

    <div class="grid lg:grid-cols-3 gap-10">

        {{-- ── LEFT: Images ── --}}
        <div class="lg:col-span-2" x-data="{ active: 0 }">

            {{-- Main image --}}
            <div class="aspect-[4/3] overflow-hidden rounded-sm bg-ink/5 mb-3">
                @if($housePlan->images->isEmpty())
                    <x-image-frame
                        :src="null"
                        :label="'PLAN NO. '.str_pad($housePlan->id, 4, '0', STR_PAD_LEFT)"
                        note="Drawings & renders to be uploaded"
                    />
                @else
                    @foreach($housePlan->images as $i => $img)
                        <img
                            src="{{ $img->url }}"
                            alt="{{ $housePlan->name }}"
                            class="w-full h-full object-cover"
                            x-show="active === {{ $i }}"
                        >
                    @endforeach
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($housePlan->images->count() > 1)
                <div class="flex gap-2">
                    @foreach($housePlan->images as $i => $img)
                        <button
                            @click="active = {{ $i }}"
                            class="w-20 h-16 rounded-sm overflow-hidden border-2 transition-colors"
                            :class="active === {{ $i }} ? 'border-draft' : 'border-transparent'"
                        >
                            <img src="{{ $img->url }}" alt="" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Description --}}
            <div class="mt-8 prose prose-sm max-w-none text-ink/80">
                <h2 class="font-display text-xl font-semibold text-ink mb-3">About this plan</h2>
                <p class="leading-relaxed">{{ $housePlan->description }}</p>
            </div>
        </div>

        {{-- ── RIGHT: Info + CTA ── --}}
        <div class="space-y-5">

            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest">
                PLAN NO. {{ str_pad($housePlan->id, 4, '0', STR_PAD_LEFT) }}
                &nbsp;·&nbsp;
                {{ $housePlan->view_count }} views
            </p>

            <h1 class="font-display text-2xl font-semibold text-ink leading-snug">
                {{ $housePlan->name }}
            </h1>

            <div class="flex flex-wrap gap-1">
                @foreach($housePlan->categories as $cat)
                    <a href="/plans?category={{ $cat->slug }}" class="font-mono text-[11px] uppercase tracking-wider bg-draft/10 text-draft px-2 py-1 rounded-sm hover:bg-draft/20 transition-colors">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-3">
                @foreach([
                    ['Bedrooms',   $housePlan->bedrooms],
                    ['Bathrooms',  $housePlan->bathrooms],
                    ['Floors',     $housePlan->floors],
                    ['Floor Area', number_format($housePlan->floor_area).' sqft'],
                    ['Style',      $housePlan->style ?? '—'],
                    ['Price',      'Rs. '.number_format($housePlan->price)],
                ] as [$label, $value])
                    <div class="bg-white border border-ink/10 rounded-sm p-3">
                        <p class="font-mono text-[10px] uppercase tracking-wider text-ink/40 mb-1">{{ $label }}</p>
                        <p class="font-display font-semibold text-ink text-sm">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="space-y-3 pt-2">
                <a
                    href="/inquire?plan={{ $housePlan->id }}&name={{ urlencode($housePlan->name) }}"
                    class="block w-full text-center bg-ink text-paper font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors"
                >
                    Request a Quote for this Plan
                </a>
                <a
                    href="https://wa.me/{{ preg_replace('/\D/', '', \App\Models\SystemSetting::get('whatsapp_number', '+94717261930')) }}?text={{ urlencode('Hi, I am interested in: '.$housePlan->name.' (Plan No. '.str_pad($housePlan->id,4,'0',STR_PAD_LEFT).')') }}"
                    target="_blank"
                    class="block w-full text-center border border-[#25D366] text-[#25D366] font-medium py-3 rounded-sm hover:bg-[#25D366] hover:text-white transition-colors"
                >
                    Ask on WhatsApp
                </a>
            </div>
        </div>
    </div>

    {{-- ── Related plans ── --}}
    @if($related->isNotEmpty())
        <div class="mt-16">
            <h2 class="font-display text-xl font-semibold text-ink mb-6">Similar Plans</h2>
            <div class="grid sm:grid-cols-3 gap-6">
                @foreach($related as $plan)
                    <a href="/plans/{{ $plan->id }}" class="group block bg-white border border-ink/10 rounded-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-[4/3] overflow-hidden">
                            <x-image-frame :src="$plan->primaryImage?->url" :alt="$plan->name" />
                        </div>
                        <div class="p-4">
                            <p class="font-display font-semibold text-ink text-sm">{{ $plan->name }}</p>
                            <x-spec-readout :items="[$plan->bedrooms.' BR', $plan->bathrooms.' BA', number_format($plan->floor_area).' SQFT']" class="mt-1" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>

@endsection
