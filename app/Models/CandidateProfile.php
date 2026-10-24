<?php

namespace App\Models;

use App\Support\Encryption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    protected $fillable = [
        'user_id', 'profession_id', 'specialty_id',
        'current_country', 'uk_location_preference', 'experience_years',
        'registration_body', 'registration_status', 'uk_registration_status',
        'sponsorship_required', 'nhs_band_preference',
        'profile_photo_path', 'professional_summary',
        'onboarding_completed_at',
    ];

    protected $hidden = [
        'registration_number_encrypted',
    ];

    protected function casts(): array
    {
        return [
            'sponsorship_required'      => 'boolean',
            'onboarding_completed_at'   => 'datetime',
        ];
    }

    // Never expose the registration number directly — use setters/getters with encryption
    public function setRegistrationNumber(string $number): void
    {
        $this->registration_number_encrypted = Encryption::encrypt($number);
        $this->save();
    }

    public function getRegistrationNumber(): ?string
    {
        if (empty($this->registration_number_encrypted)) {
            return null;
        }
        return Encryption::decrypt($this->registration_number_encrypted);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }
}
