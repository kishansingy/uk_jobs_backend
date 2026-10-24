<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30); // cv, supporting_statement, certificate, registration, other
            $table->string('original_filename');
            $table->string('storage_path');
            $table->string('disk', 20)->default('private');
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->boolean('is_current')->default(false);
            $table->timestamp('uploaded_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_current']);
        });

        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('candidate_documents')->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('jobs')->nullOnDelete();
            $table->json('structure_feedback')->nullable();
            $table->json('experience_feedback')->nullable();
            $table->json('skills_feedback')->nullable();
            $table->json('missing_evidence')->nullable();
            $table->json('recommendations')->nullable();
            $table->string('ai_provider', 50)->nullable();
            $table->timestamp('analysed_at');
            $table->timestamps();

            $table->index(['user_id', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_analyses');
        Schema::dropIfExists('candidate_documents');
    }
};
