<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('job_sources')->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('title');
            $table->string('organisation');
            $table->longText('description');
            $table->string('location');
            $table->string('postcode', 15)->nullable();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_text', 200)->nullable();
            $table->string('band', 20)->nullable();
            $table->foreignId('profession_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('specialty_id')->nullable()->constrained()->nullOnDelete();
            $table->string('contract_type', 50)->nullable();
            $table->string('employment_type', 50)->nullable();
            $table->text('sponsorship_information')->nullable();
            $table->text('requirements')->nullable();
            $table->text('qualifications')->nullable();
            $table->string('experience_required', 100)->nullable();
            $table->date('closing_date')->nullable();
            $table->date('published_date')->nullable();
            $table->string('source_url')->nullable();
            $table->string('raw_reference')->nullable();
            $table->string('status', 30)->default('draft');
            $table->string('classification_method', 20)->nullable();
            $table->decimal('classification_confidence', 4, 3)->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            // Deduplication index
            $table->unique(['source_id', 'external_id']);

            // Query indexes
            $table->index('status');
            $table->index('profession_id');
            $table->index('specialty_id');
            $table->index('band');
            $table->index('closing_date');
            $table->index('published_date');
            $table->index(['status', 'published_date']);
            $table->index(['status', 'closing_date']);

            // Full-text search (MySQL/PostgreSQL only — skipped on SQLite)
            if (! app()->environment('testing') && \DB::getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'description', 'organisation', 'location']);
            }
        });

        Schema::create('job_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50); // registration, qualification, experience, skill, language
            $table->string('value');
            $table->boolean('mandatory')->default(true);
            $table->timestamps();

            $table->index('job_id');
        });

        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->string('skill');
            $table->string('level', 30)->nullable();
            $table->timestamps();

            $table->index('job_id');
        });

        Schema::create('job_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('job_sources')->cascadeOnDelete();
            $table->string('status', 20); // running, completed, failed
            $table->integer('records_fetched')->default(0);
            $table->integer('records_created')->default(0);
            $table->integer('records_updated')->default(0);
            $table->integer('records_skipped')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('source_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_import_logs');
        Schema::dropIfExists('job_skills');
        Schema::dropIfExists('job_requirements');
        Schema::dropIfExists('jobs');
    }
};
