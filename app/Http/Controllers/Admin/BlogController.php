<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::latest()->paginate(15);

        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        BlogPost::create([
            'title'        => $validated['title'],
            'slug'         => $this->uniqueSlug($validated['title']),
            'content'      => $validated['content'],
            'status'       => $validated['status'],
            'cover_image'  => $this->storeCover($request),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        return redirect('/admin/blog')->with('success', 'Post created successfully.');
    }

    public function edit(BlogPost $post): View
    {
        return view('admin.blog.form', compact('post'));
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $validated = $this->validated($request);

        $cover = $this->storeCover($request);

        if ($cover && $post->cover_image && ! str_starts_with($post->cover_image, 'http')) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->update([
            'title'        => $validated['title'],
            'slug'         => $post->title === $validated['title']
                ? $post->slug
                : $this->uniqueSlug($validated['title'], $post->id),
            'content'      => $validated['content'],
            'status'       => $validated['status'],
            'cover_image'  => $cover ?? $post->cover_image,
            // Keep the original publish date if it was already live.
            'published_at' => $validated['status'] === 'published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        return redirect('/admin/blog')->with('success', 'Post updated successfully.');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        if ($post->cover_image && ! str_starts_with($post->cover_image, 'http')) {
            Storage::disk('public')->delete($post->cover_image);
        }

        $post->delete();

        return redirect('/admin/blog')->with('success', 'Post deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'status'      => 'required|in:draft,published',
            'cover_image' => 'nullable|image|max:5120',
        ]);
    }

    private function storeCover(Request $request): ?string
    {
        return $request->hasFile('cover_image')
            ? $request->file('cover_image')->store('blog', 'public')
            : null;
    }

    /**
     * Slugs are unique in the DB, so append a counter when a title collides.
     */
    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $n    = 2;

        while (BlogPost::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$n}";
            $n++;
        }

        return $slug;
    }
}
