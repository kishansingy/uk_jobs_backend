<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('profession')->nullable();
            $table->string('specialty')->nullable();
            $table->string('experience_summary')->nullable();
            $table->string('role')->nullable();
            $table->string('organisation')->nullable(); // optional — no implied endorsement
            $table->text('biography')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('verification_status', 30)->default('pending'); // pending, verified, rejected
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contributor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained('video_categories')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_url');
            $table->string('thumbnail_url')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('status', 20)->default('draft'); // draft, review, published, archived
            $table->boolean('premium_only')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'premium_only']);
            $table->index('category_id');
        });

        Schema::create('video_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained()->cascadeOnDelete();
            $table->string('tag', 100);
            $table->timestamps();

            $table->index(['video_id', 'tag']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_tags');
        Schema::dropIfExists('videos');
        Schema::dropIfExists('contributors');
        Schema::dropIfExists('video_categories');
    }
};
