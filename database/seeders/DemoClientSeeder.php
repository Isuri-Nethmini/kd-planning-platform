<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * One demo client mid-way through a build, so every part of the portal has
 * something to show: completed and in-progress stages with photos, a verified
 * payment, a receipt waiting for review, and instalments still due.
 *
 * Sign in at /portal/login as client@kdplanning.test / password123.
 * Development data only — remove before going live.
 */
class DemoClientSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::create([
            'name'                 => 'Ruwan Fernando',
            'email'                => 'client@kdplanning.test',
            'phone'                => '+94 77 123 4567',
            'password'             => 'password123',
            'must_change_password' => false,   // so the demo goes straight in
        ]);

        $project = $customer->projects()->create([
            'title'                => 'Modern two-storey home, Ja-Ela',
            'site_location'        => 'Ja-Ela, Gampaha',
            'house_plan_id'        => 2,
            'status'               => 'active',
            'contract_value'       => 8750000,
            'start_date'           => now()->subMonths(3)->startOfMonth(),
            'estimated_completion' => now()->addMonths(5)->endOfMonth(),
        ]);
        $project->createDefaultStages();

        $stages = $project->stages()->get()->keyBy('name');

        $stages['Site preparation']->update([
            'status' => 'completed', 'started_on' => now()->subMonths(3), 'completed_on' => now()->subMonths(3)->addWeeks(2),
            'note' => 'Site cleared and levelled. Boundary marked out with the surveyor.',
        ]);
        $stages['Foundation']->update([
            'status' => 'completed', 'started_on' => now()->subMonths(2)->subWeek(), 'completed_on' => now()->subMonth(),
            'note' => 'Strip foundation complete and inspected. Damp-proof course laid.',
        ]);
        $stages['Structure and walls']->update([
            'status' => 'in_progress', 'started_on' => now()->subMonth()->addDays(3),
            'note' => 'Ground-floor walls complete. First-floor slab is scheduled for next week.',
        ]);

        // Site photos go on the private disk, exactly as real uploads would.
        $this->photo($stages['Foundation'], 'charitha-dawamottawa.jpg', 'Foundation trench and footing');
        $this->photo($stages['Structure and walls'], 'malik-adiambalama.jpg', 'Design reference for the finished elevation');

        $advance    = $project->instalments()->create(['label' => 'Advance',    'amount' => 2500000, 'due_date' => now()->subMonths(3), 'sort_order' => 1]);
        $foundation = $project->instalments()->create(['label' => 'Foundation', 'amount' => 2000000, 'due_date' => now()->subWeeks(2), 'sort_order' => 2]);
        $project->instalments()->create(['label' => 'Structure', 'amount' => 2000000, 'due_date' => now()->addWeeks(3),  'sort_order' => 3]);
        $project->instalments()->create(['label' => 'Final',     'amount' => 2250000, 'due_date' => now()->addMonths(5), 'sort_order' => 4]);

        $this->receipt($advance, 'advance-transfer.png', 2500000, now()->subMonths(3)->addDay(), 'BOC-448812', 'verified');
        $this->receipt($foundation, 'foundation-transfer.png', 2000000, now()->subDays(2), 'BOC-512094', 'pending');
    }

    private function photo($stage, string $mediaFile, string $caption): void
    {
        $source = public_path("media/projects/{$mediaFile}");
        if (! File::exists($source)) {
            return;
        }

        $path = "progress/project-{$stage->client_project_id}/demo-{$stage->id}.jpg";
        Storage::disk('local')->put($path, File::get($source));

        $stage->photos()->create(['file_path' => $path, 'caption' => $caption]);
    }

    private function receipt($instalment, string $name, int $amount, $paidOn, string $reference, string $status): void
    {
        $path = "receipts/project-{$instalment->client_project_id}/demo-{$instalment->id}.png";
        Storage::disk('local')->put($path, $this->placeholderReceipt());

        $instalment->receipts()->create([
            'file_path'     => $path,
            'original_name' => $name,
            'amount_paid'   => $amount,
            'paid_on'       => $paidOn,
            'reference'     => $reference,
            'status'        => $status,
            'reviewed_by'   => $status === 'pending' ? null : AdminUser::where('role', 'primary')->value('id'),
            'reviewed_at'   => $status === 'pending' ? null : now()->subMonths(3)->addDays(2),
        ]);
    }

    /** A small valid PNG, so the demo needs no image library. */
    private function placeholderReceipt(): string
    {
        return base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAIAAABLbSncAAAAEklEQVR4nGP4z8CAFTEMbQkAI/I/wR2bGCMAAAAASUVORK5CYII='
        );
    }
}
