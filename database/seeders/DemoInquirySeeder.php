<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use Illuminate\Database\Seeder;

/**
 * Realistic inquiries spread across the last six months and every stage of the
 * sales pipeline, so the dashboard, analytics, reports and pipeline all have
 * something meaningful to show. Development data only.
 */
class DemoInquirySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // [days ago, name, email, phone, plan, project, message, status, quoted, notes]
            [170, 'Ruwan Fernando', 'ruwan.fernando@example.com', '+94 77 123 4567', 2, null,
                'We have 15 perches in Ja-Ela and like this two-storey design. Could we add a study room upstairs?',
                'converted', 8750000, 'Site visit done. Added study upstairs. Contract signed — client portal account created.'],
            [150, 'Dilani Perera', 'dilani.p@example.com', '+94 71 220 3344', 1, null,
                'Is this plan suitable for a 10 perch land in Gampaha?', 'converted', 5200000, 'Advance received. Construction starting next month.'],
            [128, 'Mahesh Jayawardena', 'mahesh.j@example.com', '+94 76 445 1290', null, 2,
                "I like Mr. Malik's house in Adiambalama. Can you build something similar but with three bedrooms?",
                'quoted', 9800000, 'Quoted for a 3-bedroom variation. Waiting for his bank loan approval.'],
            [110, 'Nimal Silva', 'nimal.silva@example.com', '+94 77 901 2277', 7, null,
                'Interested in the Grand Colonial Estate. What is the total cost to build in Kurunegala?',
                'closed', 14500000, 'Budget too high for the client at this time. Follow up next year.'],
            [95, 'Sanduni Wickramasinghe', 'sanduni.w@example.com', '+94 72 334 8812', 4, null,
                'Looking for a budget house plan. Can the price be reduced if we use our own labour?',
                'quoted', 3900000, 'Quoted with client-supplied labour. She will confirm after visiting the bank.'],
            [80, 'Chaminda Rathnayake', 'chaminda.r@example.com', '+94 70 556 7731', 5, null,
                'Do you provide the approved drawings for the local authority as well?', 'read', null, null],
            [62, 'Tharushi Gunasekara', 'tharushi.g@example.com', '+94 77 812 4409', null, 4,
                "Thilina's single storey house is lovely. We have a corner plot in Pansilgoda — can it be adapted?",
                'quoted', 6400000, 'Adapted the layout for a corner plot. Revised quote sent.'],
            [45, 'Asanka Bandara', 'asanka.b@example.com', '+94 75 664 2201', 10, null,
                'Can this plan be built on sloping land? Our land in Kandy is on a hillside.', 'read', null, 'Asked for a land survey report before quoting.'],
            [30, 'Kavindya Senanayake', 'kavindya.s@example.com', '+94 71 778 9034', 9, null,
                'We would like to visit your office to discuss this bungalow plan.', 'quoted', 5600000, 'Office visit on Saturday.'],
            [18, 'Pradeep Kumara', 'pradeep.k@example.com', '+94 77 345 1188', 3, null,
                'How long does construction usually take for this plan?', 'read', null, null],
            [9, 'Ishara Madushani', 'ishara.m@example.com', '+94 76 112 5543', 6, null,
                'Can you send me the floor plan with dimensions? We have 12 perches in Minuwangoda.', 'new', null, null],
            [4, 'Lahiru Dissanayake', 'lahiru.d@example.com', '+94 70 998 2210', null, null,
                'I need a house plan for a narrow 8 perch block. Do you design custom plans?', 'new', null, null],
            [1, 'Nadeeka Herath', 'nadeeka.h@example.com', '+94 72 667 3390', 2, null,
                'Is a site visit included in the construction estimate?', 'new', null, null],
        ];

        foreach ($rows as [$daysAgo, $name, $email, $phone, $plan, $project, $message, $status, $quoted, $notes]) {
            $created = now()->subDays($daysAgo)->setTime(rand(8, 18), rand(0, 59));

            $inquiry = Inquiry::create([
                'name'                 => $name,
                'email'                => $email,
                'phone'                => $phone,
                'house_plan_id'        => $plan,
                'completed_project_id' => $project,
                'message'              => $message,
                'status'               => $status,
                'quoted_amount'        => $quoted,
                'admin_notes'          => $notes,
                'responded_at'         => in_array($status, ['quoted', 'converted', 'closed']) ? (clone $created)->addDays(2) : null,
            ]);

            // created_at is not mass-assignable, so set it directly.
            $inquiry->forceFill(['created_at' => $created, 'updated_at' => $created])->saveQuietly();
        }
    }
}
