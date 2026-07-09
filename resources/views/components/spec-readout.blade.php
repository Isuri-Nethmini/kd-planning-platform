@props(['items' => []])

<div {{ $attributes->merge(['class' => 'flex items-center font-mono text-[13px] text-ink/80']) }}>
    @foreach ($items as $item)
        <span>{{ $item }}</span>
        @unless ($loop->last)
            <span class="mx-2 h-3 w-px bg-clay/40 shrink-0"></span>
        @endunless
    @endforeach
</div>
