@extends('admin.layout')

@section('title', isset($testimonial) ? 'Edit Testimonial' : 'Add Testimonial')

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/testimonials" class="text-sm text-ink/50 hover:text-draft">← Testimonials</a>
    <span class="text-ink/20">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">
        {{ isset($testimonial) ? 'Edit Testimonial' : 'Add Testimonial' }}
    </h1>
</div>

<form method="POST"
      action="{{ isset($testimonial) ? '/admin/testimonials/'.$testimonial->id : '/admin/testimonials' }}"
      class="space-y-6 max-w-2xl">
    @csrf
    @if(isset($testimonial)) @method('PUT') @endif

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-4 rounded-sm">
            <p class="font-semibold mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Client Name *</label>
                <input type="text" name="client_name" value="{{ old('client_name', $testimonial->client_name ?? '') }}"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            </div>
            <div>
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Location</label>
                <input type="text" name="location" value="{{ old('location', $testimonial->location ?? '') }}" placeholder="Negombo"
                    class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
            </div>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Rating</label>
            <select name="rating" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                <option value="">No rating</option>
                @foreach([5,4,3,2,1] as $r)
                    <option value="{{ $r }}" {{ (string) old('rating', $testimonial->rating ?? '') === (string) $r ? 'selected' : '' }}>
                        {{ str_repeat('★', $r) }} ({{ $r }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Testimonial *</label>
            <textarea name="content" rows="5"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft resize-none">{{ old('content', $testimonial->content ?? '') }}</textarea>
        </div>

        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }} class="accent-draft w-4 h-4">
            <span class="text-sm">Show on the homepage</span>
        </label>
    </div>

    <button type="submit" class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">
        {{ isset($testimonial) ? 'Save Changes' : 'Add Testimonial' }}
    </button>
</form>

@endsection
