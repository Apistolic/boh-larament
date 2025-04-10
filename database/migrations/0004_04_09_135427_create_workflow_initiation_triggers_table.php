<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_initiation_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_type'); // contact_created, contact_updated, lifecycle_stage_changed, manual
            $table->json('criteria')->nullable(); // e.g. { stage: 'donor_candidate' }
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Move existing trigger data to the new table
        Schema::table('workflows', function (Blueprint $table) {
            // We'll keep these columns for now and remove them in a future migration
            // after we've verified all data is properly migrated
            $table->boolean('legacy_trigger')->default(false)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_initiation_triggers');
        
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropColumn('legacy_trigger');
        });
    }
};
