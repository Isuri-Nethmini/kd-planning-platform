@extends('layouts.app')

@section('title', 'Request a Quote')

@section('content')

<div class="bg-ink text-paper py-12">
    <div class="max-w-6xl mx-auto px-5">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-draft mb-2">Get in Touch</p>
        <h1 class="font-display text-3xl md:text-4xl font-semibold">Request a Construction Estimate</h1>
        <p class="text-paper/60 mt-3 max-w-xl leading-relaxed text-sm">
            Plan prices cover the drawings. Tell us about your land and finishes and we'll estimate the build cost.
        </p>
    </div>
</div>

<div class="max-w-2xl mx-auto px-5 py-14">

    @if($project)
        <div class="bg-clay/10 border border-clay/30 rounded-sm p-4 mb-8 flex items-center gap-4">
            <div class="w-16 h-14 rounded-sm overflow-hidden shrink-0">
                <x-image-frame :src="$project->primaryImage?->url" :alt="$project->title" :zoom="false" note="" />
            </div>
            <div>
                <p class="font-mono text-xs text-clay uppercase tracking-wider">Wants a design similar to</p>
                <p class="font-display font-semibold text-ink">{{ $project->title }}</p>
                @if($project->location)
                    <p class="font-mono text-[11px] text-ink/40">{{ $project->location }}</p>
                @endif
            </div>
        </div>
    @endif

    @if($plan)
        <div class="bg-draft/10 border border-draft/30 rounded-sm p-4 mb-8 flex items-center gap-4">
            <div class="w-16 h-14 rounded-sm overflow-hidden shrink-0">
                <x-image-frame :src="$plan->primaryImage?->url" :alt="$plan->name" :zoom="false" note="" />
            </div>
            <div>
                <p class="font-mono text-xs text-draft uppercase tracking-wider">Inquiring about</p>
                <p class="font-display font-semibold text-ink">{{ $plan->name }}</p>
                <x-spec-readout :items="[$plan->bedrooms.' BR', $plan->bathrooms.' BA', number_format($plan->floor_area).' SQFT']" />
            </div>
        </div>
    @endif

    <form method="POST" action="/inquire" class="space-y-5">
        @csrf

        @if($plan)
            <input type="hidden" name="house_plan_id" value="{{ $plan->id }}">
        @endif

        @if($project)
            <input type="hidden" name="completed_project_id" value="{{ $project->id }}">
        @endif

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Full Name *</label>
                <input
                    type="text" name="name" value="{{ old('name') }}"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('name') border-clay @enderror"
                    placeholder="Your full name"
                >
                @error('name')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Phone Number *</label>
                <input
                    type="tel" name="phone" value="{{ old('phone') }}" pattern="[+]?[0-9\s\-\(\)]{7,20}"
                    title="Enter a valid phone number (e.g. +94 71 726 1930)"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('phone') border-clay @enderror"
                    placeholder="+94 7X XXX XXXX"
                >
                @error('phone')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Email Address *</label>
            <input
                type="email" name="email" value="{{ old('email') }}"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('email') border-clay @enderror"
                placeholder="your@email.com"
            >
            @error('email')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        @if(!$plan)
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Plan of Interest (optional)</label>
                <input
                    type="text" name="plan_name" value="{{ old('plan_name') }}"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft"
                    placeholder="Plan name or leave blank for general inquiry"
                >
            </div>
        @endif

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Message *</label>
            <textarea
                name="message" rows="5"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft resize-none @error('message') border-clay @enderror"
                placeholder="Tell us about your land size, budget, timeline, or any special requirements…"
            >{{ old('message') }}</textarea>
            @error('message')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <button
            type="submit"
            class="w-full bg-ink text-paper font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors"
        >
            Submit Inquiry
        </button>

        <p class="text-center font-mono text-xs text-ink/40">No login required. We'll get back to you within 24 hours.</p>
    </form>
</div>

@endsection
