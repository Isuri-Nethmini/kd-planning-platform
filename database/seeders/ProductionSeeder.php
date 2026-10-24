<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Support\PasswordPolicy;
use Illuminate\Database\Seeder;

/**
 * Seeds the LIVE website with real content only.
 *
 *   php artisan db:seed --class=ProductionSeeder --force
 *
 * Adds the plan categories, the client's real completed projects, the
 * business contact settings and one primary admin account. It does NOT add
 * the sample house plans, sample testimonials, sample blog posts, the staff
 * test account or the demo client: those are for development and demos only
 * (DatabaseSeeder). The owner adds real plans, testimonials and posts from the
 * admin panel after launch.
 *
 * The admin gets a random temporary password, shown once in the terminal, and
 * must replace it at first sign-in.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            CompletedProjectSeeder::class,
            SystemSettingSeeder::class,
        ]);

        $email = env('ADMIN_EMAIL', 'kdplanningdesign@gmail.com');

        if (AdminUser::where('email', $email)->exists()) {
            $this->command->warn("An admin with {$email} already exists; no new account was created.");

            return;
        }

        $temporary = PasswordPolicy::temporary();

        AdminUser::create([
            'name'                 => env('ADMIN_NAME', 'Dhanushka Chathuranga De Soysa'),
            'email'                => $email,
            'password'             => $temporary,   // hashed by the model cast
            'role'                 => 'primary',
            'must_change_password' => true,
        ]);

        $this->command->newLine();
        $this->command->info('Primary admin account created.');
        $this->command->line("  Sign in at:          /admin/login");
        $this->command->line("  Email:               {$email}");
        $this->command->line("  Temporary password:  {$temporary}");
        $this->command->warn('  Copy this password now. It is shown only once and must be changed at first sign-in.');
        $this->command->newLine();
    }
}
