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

        // Add foreign key to workflows table
        Schema::table('workflows', function (Blueprint $table) {
            // Keep the old type column for now
            $table->renameColumn('type', 'legacy_type');
            $table->foreignId('workflow_type_id')->nullable()->after('legacy_type')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflows', function (Blueprint $table) {
            $table->dropForeign(['workflow_type_id']);
            $table->dropColumn('workflow_type_id');
            $table->renameColumn('legacy_type', 'type');
        });

        Schema::dropIfExists('workflow_types');
    }
};
