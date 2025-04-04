<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('touch_template_layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type'); // email, sms
            $table->text('html_content');
            $table->text('text_content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('touch_templates', function (Blueprint $table) {
            $table->foreignId('layout_id')->nullable()->constrained('touch_template_layouts')->nullOnDelete();
            $table->json('block_content')->nullable(); // Stores block content overrides
        });
    }

    public function down(): void
    {
        Schema::table('touch_templates', function (Blueprint $table) {
            $table->dropForeign(['layout_id']);
            $table->dropColumn(['layout_id', 'block_content']);
        });
        Schema::dropIfExists('touch_template_layouts');
    }
};
