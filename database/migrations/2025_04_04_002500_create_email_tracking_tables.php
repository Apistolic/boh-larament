<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_sends', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('subject');
            $table->text('content');
            $table->string('tracking_pixel_id', 50)->unique();
            $table->timestamp('sent_at');
            $table->timestamps();
            
            $table->index('tracking_pixel_id');
            $table->index('sent_at');
        });

        Schema::create('email_opens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('email_send_id')->constrained('email_sends')->cascadeOnDelete();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('email_client')->nullable();
            $table->string('device_type')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('opened_at');
            $table->timestamps();
            
            $table->index('opened_at');
            $table->index(['email_send_id', 'opened_at']);
        });

        Schema::create('email_clicks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('email_send_id')->constrained('email_sends')->cascadeOnDelete();
            $table->text('link_url');
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_type')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();
            
            $table->index('clicked_at');
            $table->index(['email_send_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_clicks');
        Schema::dropIfExists('email_opens');
        Schema::dropIfExists('email_sends');
    }
};
