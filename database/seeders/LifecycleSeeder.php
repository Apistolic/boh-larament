<?php

namespace Database\Seeders;

use App\Models\LifecycleCategory;
use App\Models\LifecycleStage;
use Illuminate\Database\Seeder;

class LifecycleSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Donor',
                'color' => '#16A34A', // green-600
                'stages' => [
                    ['name' => 'Donor Candidate', 'color' => '#FCD34D'], // amber-300
                    ['name' => 'Donor Active', 'color' => '#22C55E'], // green-500
                    ['name' => 'Donor Influencer', 'color' => '#15803D'], // green-700
                    ['name' => 'Donor Aggregator', 'color' => '#166534'], // green-800
                    ['name' => 'Donor Retired', 'color' => '#D1D5DB'], // gray-300
                ],
            ],
            [
                'name' => 'Gala',
                'color' => '#2563EB', // blue-600
                'stages' => [
                    ['name' => 'Gala Candidate', 'color' => '#93C5FD'], // blue-300
                    ['name' => 'Gala Attendee', 'color' => '#3B82F6'], // blue-500
                    ['name' => 'Gala Donor', 'color' => '#1D4ED8'], // blue-700
                    ['name' => 'Gala Volunteer', 'color' => '#1E40AF'], // blue-800
                ],
            ],
            [
                'name' => 'Neighbor',
                'color' => '#9333EA', // purple-600
                'stages' => [
                    ['name' => 'Neighbor Candidate', 'color' => '#D8B4FE'], // purple-300
                    ['name' => 'Neighbor Active', 'color' => '#A855F7'], // purple-500
                    ['name' => 'Neighbor Leader', 'color' => '#7E22CE'], // purple-700
                    ['name' => 'Neighbor Influencer', 'color' => '#6B21A8'], // purple-800
                    ['name' => 'Neighbor Retired', 'color' => '#D1D5DB'], // gray-300
                ],
            ],
            [
                'name' => 'Mom',
                'color' => '#DC2626', // red-600
                'stages' => [
                    ['name' => 'Mom Candidate', 'color' => '#FCA5A5'], // red-300
                    ['name' => 'Mom Active', 'color' => '#EF4444'], // red-500
                    ['name' => 'Mom Graduate', 'color' => '#B91C1C'], // red-700
                ],
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $stages = $categoryData['stages'];
            unset($categoryData['stages']);
            
            $category = LifecycleCategory::create([
                ...$categoryData,
                'sort_order' => $index,
            ]);

            foreach ($stages as $stageIndex => $stageData) {
                LifecycleStage::create([
                    ...$stageData,
                    'lifecycle_category_id' => $category->id,
                    'sort_order' => $stageIndex,
                ]);
            }
        }
    }
}
