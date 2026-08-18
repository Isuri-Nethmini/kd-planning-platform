@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', $post->excerpt)

@section('content')

<article class="max-w-3xl mx-auto px-5 py-12">

    <nav class="font-mono text-xs text-ink/50 mb-8">
        <a href="/" class="hover:text-draft">Home</a>
        <span class="mx-2">/</span>
        <a href="/blog" class="hover:text-draft">Blog</a>
    </nav>

    <p class="font-mono text-xs uppercase tracking-[0.2em] text-clay mb-3">
        {{ $post->published_at?->format('d F Y') }}
    </p>

    <h1 class="font-display text-3xl md:text-4xl font-semibold text-ink leading-tight mb-8">
        {{ $post->title }}
    </h1>

    <div class="aspect-[16/9] overflow-hidden rounded-sm bg-ink/5 mb-10">
        <x-image-frame :src="$post->coverUrl" :alt="$post->title" :zoom="false" note="Cover image pending" />
    </div>

    <div class="article-body">
        @foreach(preg_split('/\r\n\r\n|\n\n/', $post->content) as $paragraph)
            @if(trim($paragraph) !== '')
                <p>{{ $paragraph }}</p>
            @endif
        @endforeach
    </div>

    <div class="mt-12 pt-8 border-t border-ink/10">
        <a href="/inquire" class="inline-flex items-center rounded-sm bg-ink text-paper font-medium px-6 py-3 hover:bg-ink/90 transition-colors">
            Talk to us about your build
        </a>
    </div>
</article>

@if($recent->isNotEmpty())
    <section class="max-w-6xl mx-auto px-5 pb-20">
        <h2 class="font-display text-xl font-semibold text-ink mb-6">More from the blog</h2>
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($recent as $other)
                <a href="/blog/{{ $other->slug }}" class="group block">
                    <div class="aspect-[16/10] overflow-hidden rounded-sm bg-ink/5 mb-3">
                        <x-image-frame :src="$other->coverUrl" :alt="$other->title" note="Cover image pending" />
                    </div>
                    <h3 class="font-display font-semibold text-sm text-ink group-hover:text-draft transition-colors">
                        {{ $other->title }}
                    </h3>
                </a>
            @endforeach
        </div>
    </section>
@endif

@endsection
