<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load(): void
    {
        $plan = $this->makePlan();

        foreach (['/', '/plans', "/plans/{$plan->id}", '/about', '/blog', '/completed-projects', '/inquire', '/portal/login'] as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_search_never_shows_hidden_plans(): void
    {
        $this->makePlan(['name' => 'Visible Villa']);
        $this->makePlan(['name' => 'Hidden Villa', 'is_active' => false]);

        // The original bug: an ungrouped orWhere escaped the active() scope.
        $this->get('/plans?q=Villa')->assertSee('Visible Villa')->assertDontSee('Hidden Villa');
    }

    public function test_a_guest_can_send_an_inquiry_without_an_account(): void
    {
        $plan = $this->makePlan();

        $this->post('/inquire', [
            'name' => 'Kasun', 'email' => 'kasun@example.com', 'phone' => '0771234567',
            'house_plan_id' => $plan->id, 'message' => 'I have 20 perches in Kadawatha.',
        ])->assertRedirect('/inquire/success');

        $this->assertDatabaseHas('inquiries', ['email' => 'kasun@example.com', 'status' => 'new']);
    }

    public function test_a_general_inquiry_saves_the_requirements(): void
    {
        $this->get('/inquire')->assertSee('Your requirements')->assertSee('Budget');

        $this->post('/inquire', [
            'name' => 'Nimal', 'email' => 'nimal@example.com', 'phone' => '0771234567',
            'message' => 'Looking for a family home.',
            'budget' => '5m_10m', 'bedrooms' => 3, 'bathrooms' => 2, 'floors' => 2, 'land_size' => '10_20',
        ])->assertRedirect('/inquire/success');

        $this->assertDatabaseHas('inquiries', [
            'email' => 'nimal@example.com', 'budget' => '5m_10m', 'bedrooms' => 3, 'land_size' => '10_20',
        ]);
    }

    public function test_an_unknown_budget_is_rejected(): void
    {
        $this->post('/inquire', [
            'name' => 'Nimal', 'email' => 'nimal@example.com', 'phone' => '0771234567',
            'message' => 'Hi', 'budget' => 'a-million-dollars',
        ])->assertSessionHasErrors('budget');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_an_inquiry_with_an_invalid_email_is_rejected(): void
    {
        $this->post('/inquire', ['name' => 'X', 'email' => 'not-an-email', 'phone' => '077', 'message' => 'Hi'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_the_whatsapp_link_carries_the_plan(): void
    {
        $plan = $this->makePlan(['name' => 'Coconut Grove Home']);

        $this->get("/plans/{$plan->id}")->assertSee('wa.me', false)->assertSee(urlencode('Coconut Grove Home'), false);
    }
}
