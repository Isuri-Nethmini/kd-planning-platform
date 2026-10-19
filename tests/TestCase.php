<?php

namespace Tests;

use App\Models\AdminUser;
use App\Models\ClientProject;
use App\Models\Customer;
use App\Models\HousePlan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests check behaviour, not compiled CSS. Without this, every test
        // that renders a page fails unless `npm run build` was run first —
        // so a fresh checkout would show failures that aren't real.
        $this->withoutVite();

        // The password policy checks Have I Been Pwned over the network. Tests
        // must never depend on an outside service, so answer it locally:
        // an empty response means "not found in any breach".
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('', 200)]);
    }

    // ── Admin helpers ───────────────────────────────────────────

    protected function makeAdmin(string $role = 'primary', array $overrides = []): AdminUser
    {
        return AdminUser::create(array_merge([
            'name'     => ucfirst($role) . ' Admin',
            'email'    => $role . '-' . uniqid() . '@kdplanning.test',
            'password' => 'password123',
            'role'     => $role,
        ], $overrides));
    }

    /** The admin panel uses its own session-based login, so sign in by session. */
    protected function actingAsAdmin(AdminUser $admin): static
    {
        return $this->withSession([
            'admin_id'        => $admin->id,
            'admin_name'      => $admin->name,
            'admin_role'      => $admin->role,
            'admin_last_seen' => now()->timestamp,
        ]);
    }

    // ── Client helpers ──────────────────────────────────────────

    protected function makeCustomer(array $overrides = []): Customer
    {
        return Customer::create(array_merge([
            'name'                 => 'Test Client',
            'email'                => 'client-' . uniqid() . '@example.com',
            'password'             => 'password123',
            'must_change_password' => false,
        ], $overrides));
    }

    protected function makeProject(Customer $customer): ClientProject
    {
        $project = $customer->projects()->create(['title' => 'Test build', 'status' => 'active']);
        $project->createDefaultStages();

        return $project;
    }

    protected function makePlan(array $overrides = []): HousePlan
    {
        return HousePlan::create(array_merge([
            'name'        => 'Test Plan',
            'description' => 'A plan used in tests.',
            'price'       => 1000000,
            'floors'      => 1,
            'bedrooms'    => 3,
            'bathrooms'   => 2,
            'floor_area'  => 1200,
            'is_active'   => true,
        ], $overrides));
    }

    /**
     * A genuine 8×8 PNG. UploadedFile::fake()->image() needs the GD extension,
     * which is often not enabled on Windows PHP — real bytes keep the suite
     * portable, and still pass the content-based file-type check.
     */
    protected function fakePng(string $name = 'receipt.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAIAAABLbSncAAAAEklEQVR4nGP4z8CAFTEMbQkAI/I/wR2bGCMAAAAASUVORK5CYII='
        ));
    }

    // ── Mail ────────────────────────────────────────────────────

    /** Subjects of every email sent through the in-memory "array" mailer. */
    protected function sentSubjects(): array
    {
        return app('mailer')->getSymfonyTransport()->messages()
            ->map(fn ($m) => $m->getOriginalMessage()->getSubject())
            ->all();
    }
}
