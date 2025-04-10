<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,           // First: Create admin user
//            MediaSeeder::class,          // First: Seed media files
            LifecycleSeeder::class,      // First: Seed lifecycle categories and stages
            ContactSeeder::class,
            WorkflowTypeSeeder::class,
            WorkflowSeeder::class,
            WorkflowExecutionSeeder::class, // This now creates touches automatically via action handlers
            WorkflowTriggerSeeder::class,
            TouchTemplateSeeder::class,
            TouchTemplateBlocksSeeder::class, // After media: blocks use media files
            TouchTemplateLayoutsSeeder::class,// After blocks: layouts use blocks
        ]);
    }
}
