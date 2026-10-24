<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supporting_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_id')->nullable()->constrained('jobs')->nullOnDelete();
            $table->string('title')->nullable();
            $table->longText('content');
            $table->string('status', 20)->default('draft');
            $table->string('ai_provider', 50)->nullable();
            $table->json('items_requiring_confirmation')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'job_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supporting_statements');
    }
};
