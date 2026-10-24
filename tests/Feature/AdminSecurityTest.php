<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\LoginLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Admin-side security protections. */
class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_reach_the_admin_panel(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_an_admin_can_sign_in_and_it_is_logged(): void
    {
        $admin = $this->makeAdmin('primary');

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password123'])
            ->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('login_logs', ['email' => $admin->email, 'event' => 'login', 'successful' => true]);
    }

    public function test_an_account_locks_after_five_failed_attempts(): void
    {
        $admin = $this->makeAdmin('primary');

        foreach (range(1, 5) as $i) {
            $this->post('/admin/login', ['email' => $admin->email, 'password' => "wrong{$i}"]);
        }

        // Even the correct password is refused while locked.
        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'password123'])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('login_logs', ['email' => $admin->email, 'event' => 'lockout']);
    }

    public function test_staff_are_blocked_from_primary_pages_and_the_attempt_is_logged(): void
    {
        $staff = $this->makeAdmin('secondary');

        foreach (['/admin/analytics', '/admin/admins', '/admin/logs', '/admin/payments'] as $page) {
            $this->actingAsAdmin($staff)->get($page)->assertRedirect('/admin/dashboard');
        }

        $this->assertSame(4, LoginLog::where('event', 'access_denied')->count());
    }

    public function test_a_new_admin_gets_a_temporary_password_and_must_change_it(): void
    {
        $primary = $this->makeAdmin('primary');

        $this->actingAsAdmin($primary)->post('/admin/admins', [
            'name' => 'New Staff', 'email' => 'new@kdplanning.test', 'role' => 'secondary',
        ]);

        $created = AdminUser::where('email', 'new@kdplanning.test')->first();
        $this->assertTrue($created->must_change_password);
        $this->assertContains('Your KD Planning & Design admin account', $this->sentSubjects());

        // Every page except the password page is closed until it is changed.
        $this->actingAsAdmin($created)->get('/admin/dashboard')->assertRedirect('/admin/password');
        $this->actingAsAdmin($created)->get('/admin/password')->assertOk();
    }

    public function test_changing_the_password_releases_the_account(): void
    {
        $admin = $this->makeAdmin('secondary', ['must_change_password' => true]);

        $this->actingAsAdmin($admin)->put('/admin/password', [
            'current_password'      => 'password123',
            'password'              => 'Str0ng#Passw0rd',
            'password_confirmation' => 'Str0ng#Passw0rd',
        ])->assertRedirect('/admin/dashboard');

        $this->assertFalse($admin->fresh()->must_change_password);
        $this->assertDatabaseHas('login_logs', ['email' => $admin->email, 'event' => 'password_changed']);
    }

    public function test_weak_passwords_are_refused(): void
    {
        $admin = $this->makeAdmin('secondary', ['must_change_password' => true]);

        $this->actingAsAdmin($admin)->put('/admin/password', [
            'current_password'      => 'password123',
            'password'              => 'weakpass',
            'password_confirmation' => 'weakpass',
        ])->assertSessionHasErrors('password');
    }

    public function test_an_admin_deleted_while_signed_in_is_signed_out(): void
    {
        $admin = $this->makeAdmin('secondary');
        $this->actingAsAdmin($admin);
        $admin->delete();

        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_staff_cannot_see_view_counts_on_the_dashboard(): void
    {
        $this->makePlan(['view_count' => 4321]);

        $this->actingAsAdmin($this->makeAdmin('secondary'))->get('/admin/dashboard')->assertDontSee('4,321');
        $this->actingAsAdmin($this->makeAdmin('primary'))->get('/admin/dashboard')->assertOk();
    }
}
