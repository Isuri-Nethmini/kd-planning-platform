<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        // These two rows already exist from the migration (seeded as NULL) —
        // this just fills in real values for local testing.
        SystemSetting::set('whatsapp_number', '+94717261930');
        SystemSetting::set('notification_email', 'kdplanning@gmail.com');
    }
}
