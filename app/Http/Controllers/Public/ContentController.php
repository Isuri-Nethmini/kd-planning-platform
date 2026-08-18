<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\CompletedProject;
use Illuminate\View\View;

/**
 * Public-facing pages for content the admin manages:
 * the completed-projects gallery and the blog.
 */
class ContentController extends Controller
{
    public function projects(): View
    {
        $projects = CompletedProject::active()
            ->with(['primaryImage', 'images'])
            ->latest()
            ->paginate(9);

        return view('public.projects.index', compact('projects'));
    }

    public function blog(): View
    {
        $posts = BlogPost::published()->latest('published_at')->paginate(9);

        return view('public.blog.index', compact('posts'));
    }

    public function blogPost(string $slug): View
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();

        $recent = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.blog.show', compact('post', 'recent'));
    }
}
