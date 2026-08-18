<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompletedProject;
use App\Models\ProjectImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompletedProjectController extends Controller
{
    public function index(): View
    {
        $projects = CompletedProject::with('primaryImage')
            ->withCount('images')
            ->latest()
            ->paginate(15);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $project = CompletedProject::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location'    => $validated['location'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        $this->storeImages($request, $project);

        return redirect('/admin/projects')->with('success', 'Project added successfully.');
    }

    public function edit(CompletedProject $project): View
    {
        $project->load('images');

        return view('admin.projects.form', compact('project'));
    }

    public function update(Request $request, CompletedProject $project): RedirectResponse
    {
        $validated = $this->validated($request);

        $project->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'location'    => $validated['location'] ?? null,
            'is_active'   => $request->boolean('is_active'),
        ]);

        $this->storeImages($request, $project);

        return redirect('/admin/projects')->with('success', 'Project updated successfully.');
    }

    public function destroy(CompletedProject $project): RedirectResponse
    {
        Storage::disk('public')->deleteDirectory("projects/{$project->id}");
        $project->images()->delete();
        $project->delete();

        return redirect('/admin/projects')->with('success', 'Project deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'location'    => 'nullable|string|max:255',
            'is_active'   => 'boolean',
            'images'      => 'nullable|array',
            'images.*'    => 'image|max:5120',
        ]);
    }

    private function storeImages(Request $request, CompletedProject $project): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $existing = $project->images()->count();

        foreach ($request->file('images') as $i => $file) {
            $path = $file->store("projects/{$project->id}", 'public');

            ProjectImage::create([
                'completed_project_id' => $project->id,
                'image_path'           => $path,
                'is_primary'           => $existing === 0 && $i === 0,
                'sort_order'           => $existing + $i + 1,
            ]);
        }
    }
}
