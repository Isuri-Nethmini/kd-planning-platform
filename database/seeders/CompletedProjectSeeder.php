<?php

namespace Database\Seeders;

use App\Models\CompletedProject;
use App\Models\ProjectImage;
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

        foreach ($projects as $index => $data) {
            $project = CompletedProject::create([...$data, 'is_active' => true]);

            for ($i = 1; $i <= 3; $i++) {
                ProjectImage::create([
                    'completed_project_id' => $project->id,
                    'image_path' => "https://picsum.photos/seed/project{$project->id}-{$i}/800/600",
                    'is_primary' => $i === 1,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
