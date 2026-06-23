<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'client_name' => 'Saman Perera',
                'location' => 'Negombo',
                'rating' => 5,
                'content' => 'KD Planning & Design gave us exactly the home we dreamed of. The plan was practical, the pricing was fair, and the team was responsive throughout.',
            ],
            [
                'client_name' => 'Nilmini Fernando',
                'location' => 'Gampaha',
                'rating' => 5,
                'content' => 'We browsed several plans online and found the perfect single-storey design for our family. Construction quality exceeded our expectations.',
            ],
            [
                'client_name' => 'Ruwan Jayasinghe',
                'location' => 'Minuwangoda',
                'rating' => 4,
                'content' => 'Professional service from the first inquiry to handover. Highly recommend KD Planning & Design for anyone building their first home.',
            ],
            [
                'client_name' => 'Chamodi Wickramasinghe',
                'location' => 'Kandy',
                'rating' => 5,
                'content' => 'The modern villa plan we chose was beautifully executed. Great communication and attention to detail throughout the project.',
            ],
            [
                'client_name' => 'Asanka Rathnayake',
                'location' => 'Kurunegala',
                'rating' => 4,
                'content' => 'Good value for money and the construction team was skilled and punctual. Our family is very happy with the final result.',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create([...$testimonial, 'is_active' => true]);
        }
    }
}
