<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('career_advisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name');
            $table->json('specialisms')->nullable();
            $table->text('bio')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('advisor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advisor_id')->constrained('career_advisors')->cascadeOnDelete();
            $table->date('available_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('booked')->default(false);
            $table->timestamps();

            $table->index(['advisor_id', 'available_date', 'booked']);
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('advisor_id')->constrained('career_advisors')->cascadeOnDelete();
            $table->foreignId('availability_id')->nullable()->constrained('advisor_availabilities')->nullOnDelete();
            $table->string('topic', 100);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending'); // pending, confirmed, completed, cancelled
            $table->string('meeting_url')->nullable();
            $table->timestamp('booked_at');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['advisor_id', 'status']);
        });

        Schema::create('advisor_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('read')->default(false);
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['appointment_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advisor_messages');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('advisor_availabilities');
        Schema::dropIfExists('career_advisors');
    }
};
