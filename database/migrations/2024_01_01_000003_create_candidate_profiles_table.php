<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('profession_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('specialty_id')->nullable()->constrained()->nullOnDelete();
            $table->string('current_country', 100)->nullable();
            $table->string('uk_location_preference')->nullable();
            $table->string('experience_years', 20)->nullable();
            $table->string('registration_body', 100)->nullable();
            // Encrypted: GMC/NMC/GPhC number — never expose in public APIs
            $table->text('registration_number_encrypted')->nullable();
            $table->string('registration_status', 100)->nullable();
            $table->string('uk_registration_status', 100)->nullable();
            $table->boolean('sponsorship_required')->nullable();
            $table->string('nhs_band_preference', 10)->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->text('professional_summary')->nullable();
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('candidate_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('preferred_locations')->nullable();
            $table->json('contract_types')->nullable();
            $table->json('employment_types')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('preferred_band', 10)->nullable();
            $table->json('keywords')->nullable();
            $table->boolean('open_to_relocation')->default(false);
            $table->timestamps();
        });

        Schema::create('candidate_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('qualification_name');
            $table->string('institution')->nullable();
            $table->string('country_obtained', 100)->nullable();
            $table->string('year_obtained', 4)->nullable();
            $table->boolean('uk_recognised')->default(false);
            $table->boolean('verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_qualifications');
        Schema::dropIfExists('candidate_preferences');
        Schema::dropIfExists('candidate_profiles');
    }
};
