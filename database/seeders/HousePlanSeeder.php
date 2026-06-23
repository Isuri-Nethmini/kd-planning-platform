<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\HousePlan;
use App\Models\PlanImage;
use Illuminate\Database\Seeder;

class HousePlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Serene Single Storey Villa',
                'description' => 'A compact and elegant single-storey home designed for small families, featuring an open-plan living area and a private garden patio.',
                'price' => 850000, 'floors' => 1, 'bedrooms' => 2, 'bathrooms' => 1,
                'floor_area' => 1200, 'style' => 'Single Storey', 'is_featured' => true,
                'categories' => ['single-storey', 'budget-friendly'],
            ],
            [
                'name' => 'Modern Two-Storey Family Home',
                'description' => 'A spacious two-storey house with clean modern lines, large glass windows, and a dedicated family lounge upstairs.',
                'price' => 2450000, 'floors' => 2, 'bedrooms' => 4, 'bathrooms' => 3,
                'floor_area' => 2600, 'style' => 'Modern', 'is_featured' => true,
                'categories' => ['double-storey', 'modern'],
            ],
            [
                'name' => 'Colonial Heritage Residence',
                'description' => 'Inspired by traditional Sri Lankan colonial architecture, featuring wide verandas, pitched roofs, and timber detailing.',
                'price' => 3200000, 'floors' => 2, 'bedrooms' => 4, 'bathrooms' => 3,
                'floor_area' => 2900, 'style' => 'Colonial', 'is_featured' => false,
                'categories' => ['double-storey', 'colonial'],
            ],
            [
                'name' => 'Budget Starter Home',
                'description' => 'An affordable, efficiently designed home perfect for first-time homeowners, without compromising on comfort.',
                'price' => 650000, 'floors' => 1, 'bedrooms' => 2, 'bathrooms' => 1,
                'floor_area' => 950, 'style' => 'Single Storey', 'is_featured' => false,
                'categories' => ['single-storey', 'budget-friendly'],
            ],
            [
                'name' => 'Skyline Modern Villa',
                'description' => 'A bold modern villa with a rooftop terrace, floor-to-ceiling windows, and minimalist interior detailing.',
                'price' => 4800000, 'floors' => 2, 'bedrooms' => 5, 'bathrooms' => 4,
                'floor_area' => 3400, 'style' => 'Modern', 'is_featured' => true,
                'categories' => ['double-storey', 'modern', 'luxury'],
            ],
            [
                'name' => 'Tropical Courtyard House',
                'description' => 'Designed around a central courtyard for natural ventilation, blending indoor and outdoor living seamlessly.',
                'price' => 2100000, 'floors' => 1, 'bedrooms' => 3, 'bathrooms' => 2,
                'floor_area' => 1850, 'style' => 'Single Storey', 'is_featured' => false,
                'categories' => ['single-storey', 'modern'],
            ],
            [
                'name' => 'Grand Colonial Estate',
                'description' => 'A luxurious colonial-style residence with high ceilings, ornamental columns, and a grand entrance foyer.',
                'price' => 6500000, 'floors' => 2, 'bedrooms' => 6, 'bathrooms' => 5,
                'floor_area' => 4200, 'style' => 'Colonial', 'is_featured' => false,
                'categories' => ['double-storey', 'colonial', 'luxury'],
            ],
            [
                'name' => 'Compact Urban Townhouse',
                'description' => 'Designed for narrow urban plots, this efficient two-storey townhouse maximizes vertical space without feeling cramped.',
                'price' => 1750000, 'floors' => 2, 'bedrooms' => 3, 'bathrooms' => 2,
                'floor_area' => 1600, 'style' => 'Modern', 'is_featured' => false,
                'categories' => ['double-storey', 'modern', 'budget-friendly'],
            ],
            [
                'name' => 'Garden View Bungalow',
                'description' => 'A charming single-storey bungalow with wraparound garden views and a cozy front porch.',
                'price' => 1450000, 'floors' => 1, 'bedrooms' => 3, 'bathrooms' => 2,
                'floor_area' => 1700, 'style' => 'Single Storey', 'is_featured' => true,
                'categories' => ['single-storey', 'budget-friendly'],
            ],
            [
                'name' => 'Luxury Hillside Mansion',
                'description' => 'A premium two-storey mansion design optimized for sloped land, featuring panoramic views and an infinity-edge balcony.',
                'price' => 8900000, 'floors' => 2, 'bedrooms' => 6, 'bathrooms' => 6,
                'floor_area' => 5100, 'style' => 'Modern', 'is_featured' => true,
                'categories' => ['double-storey', 'modern', 'luxury'],
            ],
            [
                'name' => 'Classic Family Cottage',
                'description' => 'A warm and inviting single-storey cottage with a traditional pitched roof and a spacious kitchen-dining combo.',
                'price' => 1100000, 'floors' => 1, 'bedrooms' => 3, 'bathrooms' => 2,
                'floor_area' => 1550, 'style' => 'Single Storey', 'is_featured' => false,
                'categories' => ['single-storey', 'colonial'],
            ],
            [
                'name' => 'Contemporary Box House',
                'description' => 'A striking geometric design with cantilevered upper floor, dark cladding, and an attached double car porch.',
                'price' => 3650000, 'floors' => 2, 'bedrooms' => 4, 'bathrooms' => 3,
                'floor_area' => 2750, 'style' => 'Modern', 'is_featured' => false,
                'categories' => ['double-storey', 'modern'],
            ],
        ];

        foreach ($plans as $index => $data) {
            $categorySlugs = $data['categories'];
            unset($data['categories']);

            $plan = HousePlan::create([
                ...$data,
                'view_count' => fake()->numberBetween(5, 300),
                'is_active' => true,
            ]);

            // Attach categories
            $categoryIds = Category::whereIn('slug', $categorySlugs)->pluck('id');
            $plan->categories()->attach($categoryIds);

            // Attach 3 placeholder images per plan (replace with real uploads later)
            for ($i = 1; $i <= 3; $i++) {
                PlanImage::create([
                    'house_plan_id' => $plan->id,
                    'image_path' => "https://picsum.photos/seed/plan{$plan->id}-{$i}/800/600",
                    'is_primary' => $i === 1,
                    'sort_order' => $i,
                ]);
            }
        }
    }
}
