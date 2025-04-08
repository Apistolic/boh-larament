<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowEventsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workflow_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type');
            $table->string('workflow_type');
            $table->json('payload');
            $table->string('status')->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Add indexes for common queries
            $table->index('event_type');
            $table->index('status');
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_events');
    }
}
