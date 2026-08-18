@extends('admin.layout')

@section('title', 'Blog')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">Blog Posts</h1>
    <a href="/admin/blog/create" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">
        + New Post
    </a>
</div>

<div class="bg-white border border-ink/10 rounded-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-ink/5 border-b border-ink/10">
            <tr>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Post</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50 hidden lg:table-cell">Published</th>
                <th class="text-left px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Status</th>
                <th class="text-right px-4 py-3 font-mono text-xs uppercase tracking-wider text-ink/50">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr class="border-b border-ink/5 last:border-0">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-10 rounded-sm overflow-hidden bg-ink/5 shrink-0">
                                <x-image-frame :src="$post->coverUrl" :alt="$post->title" :zoom="false" note="" />
                            </div>
                            <div>
                                <p class="font-medium text-ink">{{ $post->title }}</p>
                                <p class="font-mono text-xs text-ink/40">/blog/{{ $post->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 font-mono text-xs text-ink/40 hidden lg:table-cell">
                        {{ $post->published_at?->format('d M Y') ?? '—' }}
                    </td>
                    <td class="px-4 py-4">
                        <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm
                            {{ $post->status === 'published' ? 'bg-moss/10 text-moss' : 'bg-ink/5 text-ink/40' }}">
                            {{ $post->status }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            @if($post->status === 'published')
                                <a href="/blog/{{ $post->slug }}" target="_blank" class="text-xs text-draft hover:underline">View</a>
                            @endif
                            <a href="/admin/blog/{{ $post->id }}/edit" class="text-xs text-ink hover:underline">Edit</a>
                            <form method="POST" action="/admin/blog/{{ $post->id }}" onsubmit="return confirm('Delete this post?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-clay hover:underline">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-10 text-center text-ink/40">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($posts->hasPages())
    <div class="mt-6">{{ $posts->links() }}</div>
@endif

@endsection
