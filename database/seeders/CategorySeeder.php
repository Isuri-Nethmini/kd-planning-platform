<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Single Storey',    'slug' => 'single-storey'],
            ['name' => 'Double Storey',    'slug' => 'double-storey'],
            ['name' => 'Modern',           'slug' => 'modern'],
            ['name' => 'Colonial',         'slug' => 'colonial'],
            ['name' => 'Budget-Friendly',  'slug' => 'budget-friendly'],
            ['name' => 'Luxury',           'slug' => 'luxury'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
