<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('trigger_type');
            $table->json('trigger_criteria')->nullable();
            $table->json('actions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->datetime('last_executed_at')->nullable();
            $table->integer('execution_count')->default(0);

            $table->text('class')->nullable();
            $table->text('arguments')->nullable();
            $table->text('output')->nullable();
            $table->string('status')->default('pending')->index();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contact_workflow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('workflow_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pending');
            $table->datetime('executed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_workflow');
        Schema::dropIfExists('workflows');
    }
};
