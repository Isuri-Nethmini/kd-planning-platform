@extends('portal.layout')

@section('title', 'My Projects')

@section('portal')
<h1 class="font-display text-2xl font-semibold text-ink mb-6">My projects</h1>

@forelse($projects as $project)
    <a href="/portal/projects/{{ $project->id }}" class="block bg-white border border-ink/15 rounded-sm p-5 mb-4 hover:border-draft transition-colors">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-display font-semibold text-ink text-lg">{{ $project->title }}</p>
                <p class="text-sm text-ink/70">{{ $project->site_location }}</p>
            </div>
            <p class="font-display text-2xl font-bold text-draft">{{ $project->progress }}%</p>
        </div>
    </a>
@empty
    <div class="bg-white border border-ink/15 rounded-sm p-10 text-center text-ink/70">
        Your project hasn't been set up yet. We'll let you know as soon as it is.
    </div>
@endforelse
@endsection
