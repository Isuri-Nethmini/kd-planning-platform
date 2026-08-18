@extends('admin.layout')

@section('title', 'Testimonials')

@section('content')

<div class="flex items-center justify-between mb-8">
    <h1 class="font-display text-2xl font-semibold text-ink">Testimonials</h1>
    <a href="/admin/testimonials/create" class="inline-flex items-center gap-2 bg-ink text-paper text-sm font-medium px-4 py-2 rounded-sm hover:bg-ink/90 transition-colors">
        + Add Testimonial
    </a>
</div>

<div class="grid md:grid-cols-2 gap-5">
    @forelse($testimonials as $t)
        <div class="bg-white border border-ink/10 rounded-sm p-5">
            <div class="flex items-start justify-between gap-4 mb-3">
                <div>
                    <p class="font-display font-semibold text-ink">{{ $t->client_name }}</p>
                    <p class="font-mono text-xs text-ink/40">{{ $t->location ?? '—' }}</p>
                </div>
                <span class="font-mono text-[11px] uppercase px-2 py-1 rounded-sm shrink-0
                    {{ $t->is_active ? 'bg-moss/10 text-moss' : 'bg-ink/5 text-ink/40' }}">
                    {{ $t->is_active ? 'Visible' : 'Hidden' }}
                </span>
            </div>

            @if($t->rating)
                <p class="text-clay text-sm mb-2">{{ str_repeat('★', $t->rating) }}<span class="text-ink/15">{{ str_repeat('★', 5 - $t->rating) }}</span></p>
            @endif

            <p class="text-sm text-ink/70 leading-relaxed mb-4">"{{ $t->content }}"</p>

            <div class="flex items-center gap-3">
                <a href="/admin/testimonials/{{ $t->id }}/edit" class="text-xs text-ink hover:underline">Edit</a>
                <form method="POST" action="/admin/testimonials/{{ $t->id }}" onsubmit="return confirm('Delete this testimonial?')">
                    @csrf @method('DELETE')
                    <button class="text-xs text-clay hover:underline">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="md:col-span-2 bg-white border border-ink/10 rounded-sm px-4 py-10 text-center text-ink/40">
            No testimonials yet.
        </div>
    @endforelse
</div>

@if($testimonials->hasPages())
    <div class="mt-6">{{ $testimonials->links() }}</div>
@endif

@endsection
