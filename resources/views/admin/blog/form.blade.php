@extends('admin.layout')

@section('title', isset($post) ? 'Edit Post' : 'New Post')

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/blog" class="text-sm text-ink/50 hover:text-draft">← Blog</a>
    <span class="text-ink/20">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">
        {{ isset($post) ? 'Edit Post' : 'New Post' }}
    </h1>
</div>

<form method="POST"
      action="{{ isset($post) ? '/admin/blog/'.$post->id : '/admin/blog' }}"
      enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(isset($post)) @method('PUT') @endif

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-4 rounded-sm">
            <p class="font-semibold mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}"
                        class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
                </div>

                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Content *</label>
                    <textarea name="content" rows="14"
                        class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">{{ old('content', $post->content ?? '') }}</textarea>
                    <p class="text-xs text-ink/40 mt-2">Leave a blank line between paragraphs.</p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-4">
                <h2 class="font-display font-semibold text-ink">Publish</h2>
                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Status</label>
                    <select name="status" class="w-full border border-ink/20 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-draft">
                        @foreach(['draft' => 'Draft', 'published' => 'Published'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $post->status ?? 'draft') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-ink text-paper font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors">
                    {{ isset($post) ? 'Save Changes' : 'Create Post' }}
                </button>
            </div>

            <div class="bg-white border border-ink/10 rounded-sm p-6">
                <h2 class="font-display font-semibold text-ink mb-4">Cover Image</h2>
                <div class="aspect-[16/10] rounded-sm overflow-hidden bg-ink/5 mb-3">
                    <x-image-frame :src="$post->coverUrl ?? null" :zoom="false" note="No cover uploaded" />
                </div>
                <input type="file" name="cover_image" accept="image/*"
                    class="w-full text-sm text-ink/60 file:mr-3 file:py-2 file:px-4 file:rounded-sm file:border file:border-ink/20 file:text-xs file:font-medium file:bg-paper hover:file:bg-ink hover:file:text-paper file:cursor-pointer file:transition-colors">
            </div>
        </div>
    </div>
</form>

@endsection
