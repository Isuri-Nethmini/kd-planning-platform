@extends('admin.layout')

@section('title', isset($project) ? 'Edit Project' : 'Add Project')

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/projects" class="text-sm text-ink/50 hover:text-draft">← Projects</a>
    <span class="text-ink/20">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">
        {{ isset($project) ? 'Edit: '.$project->title : 'Add Project' }}
    </h1>
</div>

<form method="POST"
      action="{{ isset($project) ? '/admin/projects/'.$project->id : '/admin/projects' }}"
      enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(isset($project)) @method('PUT') @endif

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-4 rounded-sm">
            <p class="font-semibold mb-2">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Project Title *</label>
            <input type="text" name="title" value="{{ old('title', $project->title ?? '') }}"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Location</label>
            <input type="text" name="location" value="{{ old('location', $project->location ?? '') }}" placeholder="Negombo, Gampaha…"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
        </div>

        <div>
            <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Description</label>
            <textarea name="description" rows="4"
                class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft resize-none">{{ old('description', $project->description ?? '') }}</textarea>
        </div>

        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_active" value="1"
                {{ old('is_active', $project->is_active ?? true) ? 'checked' : '' }} class="accent-draft w-4 h-4">
            <span class="text-sm">Visible on the public site</span>
        </label>
    </div>

    <div class="bg-white border border-ink/10 rounded-sm p-6">
        <h2 class="font-display font-semibold text-ink mb-4">Photos</h2>

        @if(isset($project) && $project->images->isNotEmpty())
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($project->images as $img)
                    <div class="relative w-24 h-20 rounded-sm overflow-hidden bg-ink/5">
                        <x-image-frame :src="$img->url" :zoom="false" note="" />
                        @if($img->is_primary)
                            <span class="absolute bottom-0 inset-x-0 text-center font-mono text-[9px] bg-draft text-white py-0.5">PRIMARY</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <input type="file" name="images[]" multiple accept="image/*"
            class="w-full text-sm text-ink/60 file:mr-3 file:py-2 file:px-4 file:rounded-sm file:border file:border-ink/20 file:text-xs file:font-medium file:bg-paper hover:file:bg-ink hover:file:text-paper file:cursor-pointer file:transition-colors">
        <p class="text-xs text-ink/40 mt-2">First photo becomes the cover. Max 5MB per image.</p>
    </div>

    <button type="submit" class="bg-ink text-paper font-medium px-8 py-3 rounded-sm hover:bg-ink/90 transition-colors">
        {{ isset($project) ? 'Save Changes' : 'Create Project' }}
    </button>
</form>

@endsection
