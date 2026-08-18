@extends('admin.layout')

@section('title', 'Completed Projects')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">Completed Projects</h1>
    <a href="/admin/projects/create" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">
        + Add Project
    </a>
</div>

<div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/10">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Project</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden md:table-cell">Location</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden lg:table-cell">Photos</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Status</th>
                <th class="text-right px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
                <tr class="border-b border-ink/5 last:border-0">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-10 rounded-sm overflow-hidden bg-ink/5 shrink-0">
                                <x-image-frame :src="$project->primaryImage?->url" :alt="$project->title" :zoom="false" note="" />
                            </div>
                            <p class="font-medium text-ink">{{ $project->title }}</p>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-ink/60 hidden md:table-cell">{{ $project->location ?? '—' }}</td>
                    <td class="px-4 py-4 font-mono text-xs text-ink/40 hidden lg:table-cell">{{ $project->images_count }}</td>
                    <td class="px-4 py-4">
                        <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm
                            {{ $project->is_active ? 'bg-moss/10 text-moss' : 'bg-ink/5 text-ink/40' }}">
                            {{ $project->is_active ? 'Visible' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="/admin/projects/{{ $project->id }}/edit" class="text-xs text-ink hover:underline">Edit</a>
                            <form method="POST" action="/admin/projects/{{ $project->id }}" onsubmit="return confirm('Delete this project and its photos?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-clay hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-10 text-center text-ink/40">No projects yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($projects->hasPages())
    <div class="mt-6">{{ $projects->links() }}</div>
@endif

@endsection
