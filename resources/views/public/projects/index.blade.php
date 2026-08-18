@extends('layouts.app')

@section('title', 'Completed Projects')
@section('meta_description', 'Homes designed and built by KD Planning & Design across Gampaha, Negombo and Minuwangoda.')

@section('content')

<div class="bg-ink text-paper py-12">
    <div class="max-w-6xl mx-auto px-5">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-draft mb-2">Our Work</p>
        <h1 class="font-display text-3xl md:text-4xl font-semibold">Completed Projects</h1>
        <p class="text-paper/60 mt-3 max-w-xl leading-relaxed">
            Homes we've designed and delivered for families across the Western Province.
        </p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-5 py-14">

    @if($projects->isEmpty())
        <div class="text-center py-24 text-ink/40">
            <p class="font-display text-lg">No completed projects published yet.</p>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($projects as $project)
                <article class="group bg-white border border-ink/10 rounded-sm overflow-hidden hover:shadow-lg hover:shadow-ink/5 transition-shadow">
                    <div class="aspect-[4/3] overflow-hidden bg-ink/5">
                        <x-image-frame
                            :src="$project->primaryImage?->url"
                            :alt="$project->title"
                            note="Project photos pending"
                        />
                    </div>
                    <div class="p-5">
                        @if($project->location)
                            <p class="font-mono text-[10px] uppercase tracking-wider text-clay mb-2">{{ $project->location }}</p>
                        @endif
                        <h2 class="font-display font-semibold text-lg text-ink mb-2 leading-snug">{{ $project->title }}</h2>
                        @if($project->description)
                            <p class="text-sm text-ink/60 leading-relaxed">{{ $project->description }}</p>
                        @endif
                        @if($project->images->count() > 1)
                            <p class="font-mono text-[10px] uppercase tracking-wider text-ink/30 mt-3">
                                {{ $project->images->count() }} photos
                            </p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if($projects->hasPages())
            <div class="mt-10 flex justify-center">
                {{ $projects->links() }}
            </div>
        @endif
    @endif

    <div class="mt-16 bg-ink text-paper rounded-sm p-10 text-center">
        <h2 class="font-display text-xl md:text-2xl font-semibold mb-3">Want a home like these?</h2>
        <p class="text-paper/70 mb-6 max-w-lg mx-auto">Browse our plan catalogue or send us your requirements and we'll come back with options.</p>
        <a href="/inquire" class="inline-flex items-center rounded-sm bg-draft text-ink font-medium px-8 py-3 hover:bg-draft/90 transition-colors">
            Request a Quote
        </a>
    </div>
</div>

@endsection
