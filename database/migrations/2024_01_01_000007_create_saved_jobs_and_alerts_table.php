<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('reminder_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'job_id']);
            $table->index('user_id');
        });

        Schema::create('job_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('filters'); // profession, specialty, location, band, salary, sponsorship, keywords
            $table->string('frequency', 20)->default('daily'); // instant, daily, weekly
            $table->boolean('active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'active']);
            $table->index(['active', 'frequency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_alerts');
        Schema::dropIfExists('saved_jobs');
    }
};
