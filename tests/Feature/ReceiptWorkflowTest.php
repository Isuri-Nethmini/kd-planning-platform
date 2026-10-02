<?php

namespace Tests\Feature;

use App\Models\PaymentReceipt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Offline payments: upload, review, and the rules around them. */
class ReceiptWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private $customer;
    private $instalment;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->customer   = $this->makeCustomer();
        $this->instalment = $this->makeProject($this->customer)
            ->instalments()->create(['label' => 'Advance', 'amount' => 250000]);
    }

    private function upload(array $overrides = [])
    {
        return $this->actingAs($this->customer, 'customer')->post(
            "/portal/instalments/{$this->instalment->id}/receipts",
            array_merge([
                'receipt'     => $this->fakePng('transfer.png'),
                'amount_paid' => 250000,
                'paid_on'     => now()->toDateString(),
                'reference'   => 'BOC-1',
            ], $overrides)
        );
    }

    public function test_a_client_can_upload_a_receipt_and_the_business_is_notified(): void
    {
        $this->upload()->assertSessionHasNoErrors();

        $receipt = PaymentReceipt::first();
        $this->assertSame('pending', $receipt->status);
        Storage::disk('local')->assertExists($receipt->file_path);
        $this->assertContains('Receipt to verify — Test Client', $this->sentSubjects());
    }

    public function test_only_images_and_pdfs_are_accepted(): void
    {
        $this->upload(['receipt' => UploadedFile::fake()->create('virus.exe', 10)])
            ->assertSessionHasErrors('receipt');

        $this->assertDatabaseCount('payment_receipts', 0);
    }

    public function test_a_payment_date_in_the_future_is_rejected(): void
    {
        $this->upload(['paid_on' => now()->addDay()->toDateString()])->assertSessionHasErrors('paid_on');
    }

    public function test_a_second_receipt_cannot_be_uploaded_while_one_is_under_review(): void
    {
        $this->upload();
        $this->upload(['reference' => 'BOC-2']);

        $this->assertDatabaseCount('payment_receipts', 1);
    }

    public function test_a_primary_admin_can_verify_a_receipt_and_the_client_is_told(): void
    {
        $this->upload();
        $admin   = $this->makeAdmin('primary');
        $receipt = PaymentReceipt::first();

        $this->actingAsAdmin($admin)->post("/admin/receipts/{$receipt->id}/verify");

        $receipt->refresh();
        $this->assertSame('verified', $receipt->status);
        $this->assertSame($admin->id, $receipt->reviewed_by);   // audit trail
        $this->assertSame('paid', $this->instalment->fresh()->status);
        $this->assertContains('Payment verified', $this->sentSubjects());
    }

    public function test_staff_admins_cannot_verify_receipts(): void
    {
        $this->upload();
        $receipt = PaymentReceipt::first();

        $this->actingAsAdmin($this->makeAdmin('secondary'))
            ->post("/admin/receipts/{$receipt->id}/verify")
            ->assertRedirect('/admin/dashboard');

        $this->assertSame('pending', $receipt->fresh()->status);
    }

    public function test_a_rejection_needs_a_reason_and_lets_the_client_upload_again(): void
    {
        $this->upload();
        $receipt = PaymentReceipt::first();
        $admin   = $this->makeAdmin('primary');

        $this->actingAsAdmin($admin)->post("/admin/receipts/{$receipt->id}/reject")
            ->assertSessionHasErrors('rejection_reason');

        $this->actingAsAdmin($admin)->post("/admin/receipts/{$receipt->id}/reject", [
            'rejection_reason' => 'Not received yet',
        ]);

        $this->assertSame('rejected', $this->instalment->fresh()->status);

        $this->upload(['reference' => 'BOC-2'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('payment_receipts', 2);
    }

    public function test_a_reviewed_receipt_cannot_be_reviewed_again(): void
    {
        $this->upload();
        $receipt = PaymentReceipt::first();
        $admin   = $this->makeAdmin('primary');

        $this->actingAsAdmin($admin)->post("/admin/receipts/{$receipt->id}/verify");
        $this->actingAsAdmin($admin)->post("/admin/receipts/{$receipt->id}/reject", ['rejection_reason' => 'x']);

        $this->assertSame('verified', $receipt->fresh()->status);
    }
}
