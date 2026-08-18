<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HousePlan;
use App\Models\PlanImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = HousePlan::with(['primaryImage', 'categories'])
            ->latest()
            ->paginate(15);

        return view('admin.plans.index', compact('plans'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.plans.form', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'floors'      => 'required|integer|min:1|max:5',
            'bedrooms'    => 'required|integer|min:1|max:20',
            'bathrooms'   => 'required|integer|min:1|max:20',
            'floor_area'  => 'required|numeric|min:1',
            'style'       => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'categories'  => 'nullable|array',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:5120',
        ]);

        $plan = HousePlan::create([
            ...collect($validated)->except(['categories', 'images'])->all(),
            'is_featured' => $request->boolean('is_featured'),
            'is_active'   => $request->boolean('is_active', true),
        ]);

        if ($request->filled('categories')) {
            $plan->categories()->sync($request->categories);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store("plans/{$plan->id}", 'public');
                PlanImage::create([
                    'house_plan_id' => $plan->id,
                    'image_path'    => $path,
                    'is_primary'    => $i === 0,
                    'sort_order'    => $i + 1,
                ]);
            }
        }

        return redirect('/admin/plans')->with('success', 'Plan created successfully.');
    }

    public function edit(HousePlan $plan): View
    {
        $categories = Category::all();
        $plan->load(['images', 'categories']);
        return view('admin.plans.form', compact('plan', 'categories'));
    }

    public function update(Request $request, HousePlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'floors'      => 'required|integer|min:1|max:5',
            'bedrooms'    => 'required|integer|min:1|max:20',
            'bathrooms'   => 'required|integer|min:1|max:20',
            'floor_area'  => 'required|numeric|min:1',
            'style'       => 'nullable|string|max:100',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
            'categories'  => 'nullable|array',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:5120',
        ]);

        $plan->update([
            ...collect($validated)->except(['categories', 'images'])->all(),
            'is_featured' => $request->boolean('is_featured'),
            'is_active'   => $request->boolean('is_active'),
        ]);

        $plan->categories()->sync($request->categories ?? []);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $file) {
                $path = $file->store("plans/{$plan->id}", 'public');
                PlanImage::create([
                    'house_plan_id' => $plan->id,
                    'image_path'    => $path,
                    'is_primary'    => false,
                    'sort_order'    => $plan->images()->count() + $i + 1,
                ]);
            }
        }

        return redirect('/admin/plans')->with('success', 'Plan updated successfully.');
    }

    public function destroy(HousePlan $plan): RedirectResponse
    {
        // Remove the plan's uploaded images from disk before dropping the rows,
        // otherwise deleted plans leave orphaned files in storage forever.
        Storage::disk('public')->deleteDirectory("plans/{$plan->id}");

        $plan->images()->delete();
        $plan->categories()->detach();
        $plan->delete();
        return redirect('/admin/plans')->with('success', 'Plan deleted.');
    }
}
