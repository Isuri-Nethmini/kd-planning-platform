<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientProject;
use App\Models\Customer;
use App\Models\HousePlan;
use App\Models\Inquiry;
use App\Models\LoginLog;
use App\Support\Notifier;
use App\Support\PasswordPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Client accounts for the portal.
 *
 * Clients never sign themselves up: the portal is for confirmed clients only,
 * so an admin creates each account — usually from an inquiry that reached
 * "Converted" in the sales pipeline.
 */
class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Customer::with(['projects.stages'])
            ->withCount('projects')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('email', 'like', '%' . $request->q . '%')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    public function create(Request $request): View
    {
        $inquiry = $request->filled('inquiry') ? Inquiry::with('housePlan')->find($request->inquiry) : null;
        $plans   = HousePlan::orderBy('name')->get(['id', 'name']);

        return view('admin.clients.create', compact('inquiry', 'plans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|max:255|unique:customers,email',
            'phone'                => 'nullable|string|max:30',
            'inquiry_id'           => 'nullable|exists:inquiries,id|unique:customers,inquiry_id',
            'title'                => 'required|string|max:255',
            'site_location'        => 'nullable|string|max:255',
            'house_plan_id'        => 'nullable|exists:house_plans,id',
            'contract_value'       => 'nullable|numeric|min:0|max:999999999',
            'start_date'           => 'nullable|date',
            'estimated_completion' => 'nullable|date|after_or_equal:start_date',
        ], [
            'inquiry_id.unique' => 'A client account already exists for this inquiry.',
        ]);

        $temporary = PasswordPolicy::temporary();

        // Account and project are created together or not at all.
        $customer = DB::transaction(function () use ($validated, $temporary) {
            $customer = Customer::create([
                'name'                 => $validated['name'],
                'email'                => $validated['email'],
                'phone'                => $validated['phone'] ?? null,
                'inquiry_id'           => $validated['inquiry_id'] ?? null,
                'password'             => $temporary,
                'must_change_password' => true,
            ]);

            $this->createProject($customer, $validated);

            return $customer;
        });

        LoginLog::recordCustomer($customer->email, LoginLog::ACCOUNT_CREATED, true, $customer, $request,
            'Created by ' . session('admin_name'));

        $emailed = Notifier::customerAccount($customer, $temporary, isNew: true);

        return redirect('/admin/client-projects/' . $customer->projects()->first()->id)->with('success',
            $emailed
                ? "Client account created. Sign-in details were emailed to {$customer->email}. Now add the payment schedule."
                : "Client account created, but the email could not be sent. Check the mail settings, then use Reset password.");
    }

    public function show(Customer $client): View
    {
        $client->load(['projects.stages', 'projects.instalments.latestReceipt', 'inquiry']);
        $plans = HousePlan::orderBy('name')->get(['id', 'name']);

        return view('admin.clients.show', compact('client', 'plans'));
    }

    public function update(Request $request, Customer $client): RedirectResponse
    {
        $client->update($request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $client->id,
            'phone' => 'nullable|string|max:30',
        ]));

        return back()->with('success', 'Client details updated.');
    }

    public function storeProject(Request $request, Customer $client): RedirectResponse
    {
        $project = $this->createProject($client, $request->validate([
            'title'                => 'required|string|max:255',
            'site_location'        => 'nullable|string|max:255',
            'house_plan_id'        => 'nullable|exists:house_plans,id',
            'contract_value'       => 'nullable|numeric|min:0|max:999999999',
            'start_date'           => 'nullable|date',
            'estimated_completion' => 'nullable|date|after_or_equal:start_date',
        ]));

        return redirect('/admin/client-projects/' . $project->id)->with('success', 'Project added.');
    }

    public function resetPassword(Request $request, Customer $client): RedirectResponse
    {
        $temporary = PasswordPolicy::temporary();

        $client->update(['password' => $temporary, 'must_change_password' => true]);

        LoginLog::recordCustomer($client->email, LoginLog::PASSWORD_RESET, true, $client, $request,
            'Reset by ' . session('admin_name'));

        $emailed = Notifier::customerAccount($client, $temporary, isNew: false);

        return back()->with('success', $emailed
            ? "A new temporary password was emailed to {$client->email}."
            : 'Password reset, but the email could not be sent. Check the mail settings.');
    }

    public function destroy(Request $request, Customer $client): RedirectResponse
    {
        LoginLog::recordCustomer($client->email, LoginLog::ACCOUNT_DELETED, true, $client, $request,
            'Deleted by ' . session('admin_name'));

        // Remove their private files too, or receipts and site photos would be
        // left on disk after every trace of them in the database is gone.
        foreach ($client->projects as $project) {
            Storage::disk('local')->deleteDirectory("receipts/project-{$project->id}");
            Storage::disk('local')->deleteDirectory("progress/project-{$project->id}");
        }

        $client->delete();   // projects, stages, photos, instalments and receipts cascade

        return redirect('/admin/clients')->with('success', 'Client account and project records deleted.');
    }

    private function createProject(Customer $customer, array $data): ClientProject
    {
        $project = $customer->projects()->create([
            'title'                => $data['title'],
            'site_location'        => $data['site_location'] ?? null,
            'house_plan_id'        => $data['house_plan_id'] ?? null,
            'contract_value'       => $data['contract_value'] ?? null,
            'start_date'           => $data['start_date'] ?? null,
            'estimated_completion' => $data['estimated_completion'] ?? null,
            'status'               => 'active',
        ]);

        $project->createDefaultStages();

        return $project;
    }
}
