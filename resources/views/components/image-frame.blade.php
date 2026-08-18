@props([
    'src'   => null,
    'alt'   => '',
    'label' => null,
    'note'  => 'Awaiting client artwork',
    'zoom'  => true,
])

{{--
    Single source of truth for every image slot on the site.

    - If $src is set, the real image renders.
    - If not, a branded blueprint placeholder renders instead, so the page
      still looks deliberate while we wait on real photography from the client.

    Drop-in usage:
        <x-image-frame :src="$plan->primaryImage?->url" :alt="$plan->name" label="PLAN NO. 0001" />
--}}

@if ($src)
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="lazy"
        class="w-full h-full object-cover {{ $zoom ? 'group-hover:scale-105 transition-transform duration-500' : '' }}"
    >
@else
    <div class="w-full h-full plan-placeholder bp-grid flex flex-col items-center justify-center text-center px-4 select-none">
        <svg viewBox="0 0 48 40" class="w-10 h-8 mb-3 text-draft/70" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect x="6" y="8" width="36" height="26" rx="1" />
            <path d="M6 27l9-8 7 6 6-5 14 11" />
            <circle cx="32" cy="16" r="3" />
        </svg>
        <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-paper/70">Image pending</p>
        <p class="font-mono text-[9px] uppercase tracking-[0.14em] text-paper/35 mt-1">{{ $note }}</p>
        @if ($label)
            <p class="font-mono text-[9px] tracking-widest text-draft/60 mt-2">{{ $label }}</p>
        @endif
    </div>
@endif
