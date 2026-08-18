@extends('layouts.app')

@section('title', 'Blog')
@section('meta_description', 'Practical advice on choosing house plans, budgeting and building in Sri Lanka.')

@section('content')

<div class="bg-ink text-paper py-12">
    <div class="max-w-6xl mx-auto px-5">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-draft mb-2">Advice & News</p>
        <h1 class="font-display text-3xl md:text-4xl font-semibold">Blog</h1>
    </div>
</div>

<div class="max-w-6xl mx-auto px-5 py-14">

    @if($posts->isEmpty())
        <div class="text-center py-24 text-ink/40">
            <p class="font-display text-lg">No posts published yet.</p>
        </div>
    @else
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($posts as $post)
                <a href="/blog/{{ $post->slug }}" class="group block">
                    <div class="aspect-[16/10] overflow-hidden rounded-sm bg-ink/5 mb-4">
                        <x-image-frame :src="$post->coverUrl" :alt="$post->title" note="Cover image pending" />
                    </div>
                    <p class="font-mono text-[10px] uppercase tracking-wider text-clay mb-2">
                        {{ $post->published_at?->format('d M Y') }}
                    </p>
                    <h2 class="font-display font-semibold text-ink leading-snug group-hover:text-draft transition-colors mb-2">
                        {{ $post->title }}
                    </h2>
                    <p class="text-sm text-ink/60 leading-relaxed">{{ $post->excerpt }}</p>
                </a>
            @endforeach
        </div>

        @if($posts->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $posts->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
