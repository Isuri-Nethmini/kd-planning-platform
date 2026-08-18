<?php

namespace Database\Seeders;

use App\Models\CompletedProject;
use App\Models\ProjectImage;
use Illuminate\Database\Seeder;

class CompletedProjectSeeder extends Seeder
{
    /**
     * Real completed projects supplied by the client (August 2026).
     *
     * Client names, locations and project costs are taken from the marketing
     * images KD Planning published themselves, so they are already public.
     * Photos live in public/media/projects/ rather than storage/, because they
     * ship with the repository as seed content rather than being uploaded
     * through the admin panel.
     */
    public function run(): void
    {
        $projects = [
            [
                'title'       => 'Mr. Charitha — Two Storey Family Home',
                'location'    => 'Dawamottawa',
                'cost'        => 4_700_000,
                'image'       => 'media/projects/charitha-dawamottawa.jpg',
                'description' => 'A compact two storey home taken from foundation through to handover. '
                               . 'The design maximises usable floor area on a narrow plot while keeping '
                               . 'an open living and dining space at ground level.',
            ],
            [
                'title'       => 'Mr. Malik — Modern Two Storey Residence',
                'location'    => 'Adiambalama',
                'cost'        => 11_500_000,
                'image'       => 'media/projects/malik-adiambalama.jpg',
                'description' => 'A modern residence with a first floor balcony, covered car porch and '
                               . 'landscaped frontage. Finished to a high specification throughout.',
            ],
            [
                'title'       => 'Mr. Shanaka — Contemporary Two Storey House',
                'location'    => 'Vatiyana',
                'cost'        => 6_900_000,
                'image'       => 'media/projects/shanaka-vatiyana.jpg',
                'description' => 'Clean contemporary lines with full height glazing, a double height '
                               . 'entrance feature and a stone clad interior dining wall.',
            ],
            [
                'title'       => 'Mr. Thilina — Single Storey Home',
                'location'    => 'Pansilgoda',
                'cost'        => 5_980_000,
                'image'       => 'media/projects/thilina-pansilgoda.jpg',
                'description' => 'A single storey home with a wide covered verandah, timber joinery and '
                               . 'a tiled pitched roof suited to the local climate.',
            ],
            [
                'title'       => 'Mrs. Kumari — Single Storey Family Home',
                'location'    => 'Kurunagala',
                'cost'        => 3_980_000,
                'image'       => 'media/projects/kumari-kurunagala.jpg',
                'description' => 'An economical single storey family home with a columned front verandah '
                               . 'and generous natural light throughout the living areas.',
            ],
        ];

        foreach ($projects as $data) {
            $project = CompletedProject::create([
                'title'       => $data['title'],
                'description' => $data['description'] . ' Project cost: Rs. ' . number_format($data['cost']) . '.',
                'location'    => $data['location'],
                'is_active'   => true,
            ]);

            ProjectImage::create([
                'completed_project_id' => $project->id,
                'image_path'           => $data['image'],
                'is_primary'           => true,
                'sort_order'           => 1,
            ]);
        }
    }
}
