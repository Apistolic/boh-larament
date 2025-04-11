<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained();
            $table->foreignId('contact_id')->constrained();
            $table->string('status'); // pending, in_progress, completed, failed
            $table->json('trigger_snapshot')->nullable(); // Store trigger data
            $table->json('results')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('workflow_action_executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_execution_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->json('parameters');
            $table->string('status'); // pending, in_progress, completed, failed
            $table->json('result')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_action_executions');
        Schema::dropIfExists('workflow_executions');
    }
};
