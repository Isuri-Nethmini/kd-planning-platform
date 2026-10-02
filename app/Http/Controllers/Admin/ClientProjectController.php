<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\ClientProject;
use App\Models\HousePlan;
use App\Models\ProjectStage;
use App\Models\StagePhoto;
use App\Support\Notifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Managing a client's build: project details, construction stages and site
 * photos. Open to all admins — staff run the site updates. The payment
 * schedule on the same page is shown and handled for primary admins only.
 */
class ClientProjectController extends Controller
{
    public function show(ClientProject $project): View
    {
        $project->load(['customer', 'housePlan', 'stages.photos', 'instalments.receipts.reviewer', 'instalments.latestReceipt']);

        return view('admin.client-projects.show', [
            'project'   => $project,
            'plans'     => HousePlan::orderBy('name')->get(['id', 'name']),
            'isPrimary' => AdminUser::find(session('admin_id'))?->isPrimary() ?? false,
        ]);
    }

    public function update(Request $request, ClientProject $project): RedirectResponse
    {
        $project->update($request->validate([
            'title'                => 'required|string|max:255',
            'site_location'        => 'nullable|string|max:255',
            'house_plan_id'        => 'nullable|exists:house_plans,id',
            'status'               => 'required|in:' . implode(',', array_keys(ClientProject::STATUSES)),
            'contract_value'       => 'nullable|numeric|min:0|max:999999999',
            'start_date'           => 'nullable|date',
            'estimated_completion' => 'nullable|date|after_or_equal:start_date',
        ]));

        return back()->with('success', 'Project details saved.');
    }

    public function updateStage(Request $request, ClientProject $project, ProjectStage $stage): RedirectResponse
    {
        abort_unless($stage->client_project_id === $project->id, 404);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'status'       => 'required|in:' . implode(',', array_keys(ProjectStage::STATUSES)),
            'started_on'   => 'nullable|date',
            'completed_on' => 'nullable|date|after_or_equal:started_on',
            'note'         => 'nullable|string|max:1000',
        ]);

        // Fill in dates the admin left blank, so the client's timeline is
        // never missing them.
        if ($validated['status'] !== 'pending' && empty($validated['started_on'])) {
            $validated['started_on'] = $stage->started_on ?? today();
        }
        if ($validated['status'] === 'completed' && empty($validated['completed_on'])) {
            $validated['completed_on'] = today();
        }
        if ($validated['status'] !== 'completed') {
            $validated['completed_on'] = null;
        }

        $statusChanged = $stage->status !== $validated['status'];
        $stage->update($validated);

        // Tell the client when a stage starts or finishes — not on every edit.
        if ($statusChanged && $stage->status !== 'pending') {
            Notifier::stageUpdated($stage->fresh('project.customer'));
        }

        return back()->with('success', "\"{$stage->name}\" updated"
            . ($statusChanged && $stage->status !== 'pending' ? ' and the client has been notified.' : '.'));
    }

    public function storeStage(Request $request, ClientProject $project): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);

        $project->stages()->create([
            'name'       => $validated['name'],
            'sort_order' => ($project->stages()->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Stage added.');
    }

    public function storePhotos(Request $request, ClientProject $project, ProjectStage $stage): RedirectResponse
    {
        abort_unless($stage->client_project_id === $project->id, 404);

        $request->validate([
            'photos'   => 'required|array|max:10',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'caption'  => 'nullable|string|max:255',
        ]);

        foreach ($request->file('photos') as $file) {
            // Private disk: these are photos of a client's home.
            $stage->photos()->create([
                'file_path' => $file->store("progress/project-{$project->id}", 'local'),
                'caption'   => $request->input('caption'),
            ]);
        }

        return back()->with('success', 'Photos added to "' . $stage->name . '".');
    }

    public function destroyPhoto(StagePhoto $photo): RedirectResponse
    {
        Storage::disk('local')->delete($photo->file_path);
        $photo->delete();

        return back()->with('success', 'Photo removed.');
    }
}
