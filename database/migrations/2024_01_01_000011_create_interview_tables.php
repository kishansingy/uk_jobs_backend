<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interview_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('profession_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('interview_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('interview_categories')->cascadeOnDelete();
            $table->foreignId('profession_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question');
            $table->text('guidance')->nullable();
            $table->string('difficulty', 20)->default('medium'); // easy, medium, hard
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'active']);
        });

        Schema::create('interview_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('jobs')->nullOnDelete();
            $table->string('title')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });

        Schema::create('interview_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('interview_sessions')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('interview_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->string('answer_audio_path')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->index('session_id');
        });

        Schema::create('interview_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->unique()->constrained('interview_answers')->cascadeOnDelete();
            $table->json('structure_feedback')->nullable();
            $table->json('relevance_feedback')->nullable();
            $table->json('clarity_feedback')->nullable();
            $table->text('overall_feedback')->nullable();
            $table->string('ai_provider', 50)->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_feedback');
        Schema::dropIfExists('interview_answers');
        Schema::dropIfExists('interview_sessions');
        Schema::dropIfExists('interview_questions');
        Schema::dropIfExists('interview_categories');
    }
};
