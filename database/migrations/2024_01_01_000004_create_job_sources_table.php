<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 30); // api, xml, json, csv, partner_feed, manual
            $table->string('base_url')->nullable();
            $table->string('source_identifier')->unique();
            $table->boolean('active')->default(false);
            $table->boolean('authorised')->default(false);
            $table->string('fetch_method', 20)->default('pull'); // pull, push
            // Encrypted API keys / credentials stored here
            $table->json('configuration')->nullable();
            $table->string('permission_status', 100)->nullable();
            $table->string('terms_reference')->nullable();
            $table->timestamp('terms_last_checked_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->timestamp('last_failure_at')->nullable();
            $table->text('last_failure_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_sources');
    }
};
