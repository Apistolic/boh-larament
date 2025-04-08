<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('phone')->nullable();
            $table->string('street')->nullable();
            $table->string('street_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state_code')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('USA');
            $table->text('notes')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('last_touched_at')->nullable();
            $table->timestamp('last_crm_update_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
