@extends('admin.layout')

@section('title', isset($plan) ? 'Edit Plan' : 'Add New Plan')

@section('content')

<div class="flex items-center gap-3 mb-8">
    <a href="/admin/plans" class="text-sm text-ink/50 hover:text-draft">← Plans</a>
    <span class="text-ink/20">/</span>
    <h1 class="font-display text-2xl font-semibold text-ink">
        {{ isset($plan) ? 'Edit: '.$plan->name : 'Add New Plan' }}
    </h1>
</div>

<form
    method="POST"
    action="{{ isset($plan) ? '/admin/plans/'.$plan->id : '/admin/plans' }}"
    enctype="multipart/form-data"
    class="space-y-8"
>
    @csrf
    @if(isset($plan)) @method('PUT') @endif

    @if($errors->any())
        <div class="bg-clay/10 border border-clay/30 text-clay text-sm px-4 py-4 rounded-sm">
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-3 gap-8">

        {{-- Left: Main fields --}}
        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-5">
                <h2 class="font-display font-semibold text-ink">Plan Details</h2>

                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Plan Name *</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name ?? '') }}"
                        class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('name') border-clay @enderror">
                    @error('name')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Description *</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft resize-none @error('description') border-clay @enderror"
                    >{{ old('description', $plan->description ?? '') }}</textarea>
                    @error('description')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Price (Rs.) *</label>
                        <input type="number" name="price" value="{{ old('price', $plan->price ?? '') }}"
                            class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('price') border-clay @enderror">
                        @error('price')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Floor Area (sqft) *</label>
                        <input type="number" name="floor_area" value="{{ old('floor_area', $plan->floor_area ?? '') }}"
                            class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('floor_area') border-clay @enderror">
                        @error('floor_area')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Bedrooms *</label>
                        <input type="number" name="bedrooms" min="1" value="{{ old('bedrooms', $plan->bedrooms ?? '') }}"
                            class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('bedrooms') border-clay @enderror">
                        @error('bedrooms')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Bathrooms *</label>
                        <input type="number" name="bathrooms" min="1" value="{{ old('bathrooms', $plan->bathrooms ?? '') }}"
                            class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft @error('bathrooms') border-clay @enderror">
                        @error('bathrooms')<p class="text-clay text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Floors *</label>
                        <select name="floors" class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
                            @foreach([1,2,3] as $f)
                                <option value="{{ $f }}" {{ old('floors', $plan->floors ?? 1) == $f ? 'selected' : '' }}>{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">Style</label>
                        <input type="text" name="style" value="{{ old('style', $plan->style ?? '') }}" placeholder="Modern, Colonial…"
                            class="w-full border border-ink/20 rounded-sm px-4 py-3 text-sm focus:outline-none focus:border-draft">
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="bg-white border border-ink/10 rounded-sm p-6">
                <h2 class="font-display font-semibold text-ink mb-4">Images</h2>
                @if(isset($plan) && $plan->images->isNotEmpty())
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($plan->images as $img)
                            <div class="relative w-24 h-20 rounded-sm overflow-hidden">
                                <img src="{{ $img->url }}" alt="" class="w-full h-full object-cover">
                                @if($img->is_primary)
                                    <span class="absolute bottom-0 left-0 right-0 text-center font-mono text-[9px] bg-draft text-white py-0.5">PRIMARY</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                <label class="font-mono text-xs uppercase tracking-wider text-ink/50 block mb-1">
                    {{ isset($plan) ? 'Add More Images' : 'Upload Images' }}
                </label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full text-sm text-ink/60 file:mr-3 file:py-2 file:px-4 file:rounded-sm file:border file:border-ink/20 file:text-xs file:font-medium file:bg-paper hover:file:bg-ink hover:file:text-paper file:cursor-pointer file:transition-colors">
                <p class="text-xs text-ink/40 mt-2">First uploaded image will be set as primary. Max 5MB per image.</p>
            </div>
        </div>

        {{-- Right: Settings --}}
        <div class="space-y-5">
            <div class="bg-white border border-ink/10 rounded-sm p-6 space-y-4">
                <h2 class="font-display font-semibold text-ink">Settings</h2>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $plan->is_active ?? true) ? 'checked' : '' }}
                        class="accent-draft w-4 h-4">
                    <span class="text-sm">Active (visible to public)</span>
                </label>

                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1"
                        {{ old('is_featured', $plan->is_featured ?? false) ? 'checked' : '' }}
                        class="accent-clay w-4 h-4">
                    <span class="text-sm">Featured on homepage ⭐</span>
                </label>
            </div>

            <div class="bg-white border border-ink/10 rounded-sm p-6">
                <h2 class="font-display font-semibold text-ink mb-4">Categories</h2>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input
                                type="checkbox"
                                name="categories[]"
                                value="{{ $cat->id }}"
                                class="accent-draft w-4 h-4"
                                {{ (isset($plan) && $plan->categories->contains($cat->id)) || (is_array(old('categories')) && in_array($cat->id, old('categories'))) ? 'checked' : '' }}
                            >
                            {{ $cat->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="w-full bg-ink text-paper font-medium py-3 rounded-sm hover:bg-ink/90 transition-colors">
                {{ isset($plan) ? 'Save Changes' : 'Create Plan' }}
            </button>
        </div>

    </div>
</form>

@endsection
