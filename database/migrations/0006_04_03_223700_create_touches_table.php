<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('touches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_execution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('touch_templates')->nullOnDelete();
            $table->string('type'); // email, sms, call, etc.
            $table->string('status'); // pending, sent, failed, etc.
            $table->json('metadata')->nullable(); // Store any additional data about the touch
            $table->text('content'); // The actual message content
            $table->string('subject')->nullable(); // Subject line for emails
            $table->timestamp('scheduled_for')->nullable(); // When this touch should occur
            $table->timestamp('executed_at')->nullable(); // When the touch actually occurred
            $table->string('error')->nullable(); // Any error message if the touch failed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('touches');
    }
};
