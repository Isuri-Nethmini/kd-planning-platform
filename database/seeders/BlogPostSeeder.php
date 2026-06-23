<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => '5 Tips for Choosing the Right House Plan for Your Land',
                'content' => 'Choosing a house plan that fits your land size, orientation, and budget is one of the most important early decisions in building a home. In this post, we walk through the key factors to consider before committing to a design — from land slope and sun direction to future expansion possibilities.',
            ],
            [
                'title' => 'Modern vs Colonial: Which Architectural Style Suits Your Family?',
                'content' => 'Sri Lankan homeowners often have to choose between sleek modern designs and timeless colonial-style homes. This article breaks down the pros, cons, and cost differences between the two styles to help you decide.',
            ],
            [
                'title' => 'Understanding Construction Costs in 2026: A Practical Guide',
                'content' => 'Material and labor costs continue to shift year on year. We break down what typically drives construction costs up or down, and how to plan your budget realistically when working with KD Planning & Design.',
            ],
            [
                'title' => 'Why a Single-Storey Home Might Be the Smarter Choice',
                'content' => 'Single-storey homes are often overlooked in favor of larger two-storey designs, but they offer real advantages — lower construction costs, easier maintenance, and better accessibility for families with children or elderly relatives.',
            ],
        ];

        foreach ($posts as $index => $post) {
            BlogPost::create([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']),
                'cover_image' => "https://picsum.photos/seed/blog{$index}/900/500",
                'content' => $post['content'],
                'status' => 'published',
                'published_at' => now()->subDays(($index + 1) * 5),
            ]);
        }
    }
}
