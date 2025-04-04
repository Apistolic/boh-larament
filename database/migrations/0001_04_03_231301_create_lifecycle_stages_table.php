<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lifecycle_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lifecycle_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('color')->nullable(); // Override category color if needed
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['lifecycle_category_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lifecycle_stages');
    }
};
