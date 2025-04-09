<?php

namespace Database\Seeders;

use App\Models\WorkflowType;
use Illuminate\Database\Seeder;

class WorkflowTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pre-defined workflow types
        $types = [
            [
                'name' => 'New Donor Candidate',
                'description' => 'Workflow for new potential donors',
                'sort_order' => 10,
            ],
            [
                'name' => 'New Active Donor',
                'description' => 'Workflow for newly converted donors',
                'sort_order' => 20,
            ],
            [
                'name' => 'New Neighboring Volunteer Candidate',
                'description' => 'Workflow for potential neighboring volunteers',
                'sort_order' => 30,
            ],
            [
                'name' => 'New NV',
                'description' => 'Workflow for new neighboring volunteers',
                'sort_order' => 40,
            ],
            [
                'name' => 'New Mom Candidate',
                'description' => 'Workflow for potential moms',
                'sort_order' => 50,
            ],
            [
                'name' => 'New Mom',
                'description' => 'Workflow for new moms',
                'sort_order' => 60,
            ],
            [
                'name' => 'Gala Candidate',
                'description' => 'Workflow for potential gala attendees',
                'sort_order' => 70,
            ],
            [
                'name' => 'Gala Attendee',
                'description' => 'Workflow for confirmed gala attendees',
                'sort_order' => 80,
            ],
            [
                'name' => 'Gala Auction Winner',
                'description' => 'Workflow for gala auction winners',
                'sort_order' => 90,
            ],
            [
                'name' => 'Gala Neighbor Signup',
                'description' => 'Workflow for neighbors who signed up at the gala',
                'sort_order' => 100,
            ],
        ];

        foreach ($types as $type) {
            WorkflowType::create($type);
        }
    }
}
