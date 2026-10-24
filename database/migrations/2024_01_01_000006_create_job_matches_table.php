<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->constrained()->cascadeOnDelete();
            $table->decimal('match_score', 5, 2)->default(0); // 0-100
            $table->json('match_factors')->nullable();
            $table->text('explanation')->nullable();
            $table->json('gaps')->nullable();
            $table->string('method', 20)->default('deterministic');
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['user_id', 'job_id']);
            $table->index(['user_id', 'match_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_matches');
    }
};
