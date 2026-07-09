<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HousePlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(Request $request): View
    {
        $query = HousePlan::active()->with(['primaryImage', 'categories']);

        // Search
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) =>
                $q->where('slug', $request->category)
            );
        }

        // Bedroom filter
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Floor filter
        if ($request->filled('floors')) {
            $query->where('floors', $request->floors);
        }

        // Sort
        match ($request->get('sort', 'newest')) {
            'price_asc'   => $query->orderBy('price', 'asc'),
            'price_desc'  => $query->orderBy('price', 'desc'),
            'most_viewed' => $query->orderBy('view_count', 'desc'),
            default       => $query->latest(),
        };

        $plans      = $query->paginate(9)->withQueryString();
        $categories = Category::all();

        return view('public.plans.index', compact('plans', 'categories'));
    }

    public function show(HousePlan $housePlan): View
    {
        abort_unless($housePlan->is_active, 404);

        $housePlan->incrementViewCount();

        $housePlan->load(['images', 'categories']);

        $related = HousePlan::active()
            ->where('id', '!=', $housePlan->id)
            ->whereHas('categories', fn($q) =>
                $q->whereIn('category_id',
                    $housePlan->categories->pluck('id')
                )
            )
            ->with('primaryImage')
            ->take(3)
            ->get();

        return view('public.plans.show', compact('housePlan', 'related'));
    }
}
