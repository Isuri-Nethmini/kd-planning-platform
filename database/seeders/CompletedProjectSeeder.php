<?php

namespace Database\Seeders;

use App\Models\CompletedProject;
use Illuminate\Database\Seeder;

class CompletedProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Perera Family Residence',
                'description' => 'A two-storey modern home completed in 2025 for a family of four in Negombo.',
                'location' => 'Negombo',
            ],
            [
                'title' => 'Fernando Single-Storey Villa',
                'description' => 'A compact single-storey villa with a private garden, completed in early 2025.',
                'location' => 'Gampaha',
            ],
            [
                'title' => 'Jayasinghe Colonial Home',
                'description' => 'A traditional colonial-style residence built for a multi-generational family.',
                'location' => 'Minuwangoda',
            ],
            [
                'title' => 'Wickramasinghe Luxury Build',
                'description' => 'A premium two-storey villa with rooftop terrace, completed in 2026.',
                'location' => 'Kandy',
            ],
        ];

        foreach ($projects as $data) {
            $project = CompletedProject::create([...$data, 'is_active' => true]);

            // Project photos are uploaded by the client via the admin panel.
        }
    }
}
