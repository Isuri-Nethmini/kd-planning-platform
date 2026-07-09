<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\HousePlan;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredPlans = HousePlan::active()
            ->featured()
            ->with(['primaryImage', 'categories'])
            ->latest()
            ->take(6)
            ->get();

        $testimonials = Testimonial::active()->latest()->take(3)->get();

        $blogPosts = BlogPost::published()->latest('published_at')->take(3)->get();

        $totalPlans = HousePlan::active()->count();

        return view('public.home', compact('featuredPlans', 'testimonials', 'blogPosts', 'totalPlans'));
    }
}
