<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ClientProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $projects = Auth::guard('customer')->user()
            ->projects()->with(['stages', 'instalments.latestReceipt'])->get();

        // Most clients have exactly one project — take them straight to it.
        if ($projects->count() === 1) {
            // Carry any flash message across this extra redirect, or messages
            // like "Your password has been changed" would be lost on the way.
            session()->reflash();

            return redirect('/portal/projects/' . $projects->first()->id);
        }

        return view('portal.index', compact('projects'));
    }

    public function show(ClientProject $project): View
    {
        // Ownership is checked on the server on every request. Hiding links is
        // not enough: without this, changing the number in the address bar
        // would show another client's project and receipts.
        //
        // 404 rather than 403, so the response does not even confirm that a
        // project with that number exists.
        abort_unless($project->customer_id === Auth::guard('customer')->id(), 404);

        $project->load([
            'housePlan',
            'stages.photos',
            'instalments.receipts',
            'instalments.latestReceipt',
        ]);

        return view('portal.project', compact('project'));
    }
}
