<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('workflow_type_id')->constrained();
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
            $table->text('sequence_diagram')->nullable();
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
        Schema::dropIfExists('workflow_types');
    }
};
