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
            LifecycleSeeder::class,      // First: Seed lifecycle categories and stages
            ContactSeeder::class,
            TouchTemplateSeeder::class,
            TouchTemplateBlocksSeeder::class, // After media: blocks use media files
            TouchTemplateLayoutsSeeder::class,// After blocks: layouts use blocks
            WorkflowTypeSeeder::class,
            WorkflowSeeder::class,
            WorkflowTriggerSeeder::class,
            WorkflowExecutionSeeder::class, // This creates touches via action handlers
            EmailTrackingSeeder::class,
        ]);
    }
}
