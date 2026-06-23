<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::create([
            'name' => 'Dhanushka Chathuranga De Soysa',
            'email' => 'kdplanning@gmail.com',
            'password' => 'password123', // auto-hashed via model cast — CHANGE before production
            'role' => 'primary',
        ]);

        AdminUser::create([
            'name' => 'Staff Admin',
            'email' => 'staff@kdplanning.test',
            'password' => 'password123', // auto-hashed via model cast — CHANGE before production
            'role' => 'secondary',
        ]);
    }
}
