<?php

namespace Tests\Feature;

use App\Models\PaymentReceipt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The client portal's security guarantees. These are the properties an
 * attacker would probe, so each one is proven by a test rather than asserted.
 */
class ClientPortalSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    private function receiptFor($project, string $status = 'pending'): PaymentReceipt
    {
        $instalment = $project->instalments()->create(['label' => 'Advance', 'amount' => 100000]);
        Storage::disk('local')->put("receipts/project-{$project->id}/r.png", 'img');

        return $instalment->receipts()->create([
            'file_path' => "receipts/project-{$project->id}/r.png", 'original_name' => 'r.png',
            'amount_paid' => 100000, 'paid_on' => now(), 'status' => $status,
        ]);
    }

    public function test_guests_are_sent_to_the_portal_login(): void
    {
        $this->get('/portal')->assertRedirect('/portal/login');
    }

    public function test_a_client_can_view_their_own_project(): void
    {
        $customer = $this->makeCustomer();
        $project  = $this->makeProject($customer);

        $this->actingAs($customer, 'customer')
            ->get("/portal/projects/{$project->id}")
            ->assertOk()
            ->assertSee('Test build');
    }

    public function test_a_client_cannot_view_another_clients_project(): void
    {
        $mine   = $this->makeCustomer();
        $theirs = $this->makeProject($this->makeCustomer());

        // 404, not 403: the response must not confirm the project exists.
        $this->actingAs($mine, 'customer')->get("/portal/projects/{$theirs->id}")->assertNotFound();
    }

    public function test_a_client_cannot_download_another_clients_receipt(): void
    {
        $mine    = $this->makeCustomer();
        $receipt = $this->receiptFor($this->makeProject($this->makeCustomer()));

        $this->actingAs($mine, 'customer')->get("/files/receipts/{$receipt->id}")->assertNotFound();
    }

    public function test_a_client_can_download_their_own_receipt(): void
    {
        $customer = $this->makeCustomer();
        $receipt  = $this->receiptFor($this->makeProject($customer));

        $this->actingAs($customer, 'customer')->get("/files/receipts/{$receipt->id}")->assertOk();
    }

    public function test_guests_cannot_download_receipts(): void
    {
        $receipt = $this->receiptFor($this->makeProject($this->makeCustomer()));

        $this->get("/files/receipts/{$receipt->id}")->assertNotFound();
    }

    public function test_a_client_cannot_upload_a_receipt_against_another_clients_instalment(): void
    {
        $mine       = $this->makeCustomer();
        $instalment = $this->makeProject($this->makeCustomer())->instalments()->create(['label' => 'Advance', 'amount' => 1000]);

        $this->actingAs($mine, 'customer')
            ->post("/portal/instalments/{$instalment->id}/receipts", [
                'receipt' => $this->fakePng('r.png'), 'amount_paid' => 1000, 'paid_on' => now()->toDateString(),
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('payment_receipts', 0);
    }

    public function test_a_client_session_cannot_reach_the_admin_panel(): void
    {
        $this->actingAs($this->makeCustomer(), 'customer')
            ->get('/admin/dashboard')
            ->assertRedirect('/admin/login');
    }

    public function test_an_admin_session_is_not_a_client_session(): void
    {
        $this->actingAsAdmin($this->makeAdmin('primary'))
            ->get('/portal')
            ->assertRedirect('/portal/login');
    }

    public function test_staff_admins_cannot_view_receipts_but_primary_admins_can(): void
    {
        $receipt = $this->receiptFor($this->makeProject($this->makeCustomer()));

        $this->actingAsAdmin($this->makeAdmin('secondary'))->get("/files/receipts/{$receipt->id}")->assertNotFound();
        $this->actingAsAdmin($this->makeAdmin('primary'))->get("/files/receipts/{$receipt->id}")->assertOk();
    }

    public function test_private_files_have_no_direct_url(): void
    {
        $receipt = $this->receiptFor($this->makeProject($this->makeCustomer()));

        $this->get('/storage/' . $receipt->file_path)->assertNotFound();
    }

    public function test_a_new_client_must_replace_their_temporary_password_first(): void
    {
        $customer = $this->makeCustomer(['must_change_password' => true]);
        $project  = $this->makeProject($customer);

        $this->actingAs($customer, 'customer')
            ->get("/portal/projects/{$project->id}")
            ->assertRedirect('/portal/password');
    }
}
