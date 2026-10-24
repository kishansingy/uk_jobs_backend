<?php

namespace App\Models;

use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Job extends Model
{
    protected $fillable = [
        'source_id', 'external_id', 'title', 'organisation', 'description',
        'location', 'postcode', 'location_id', 'salary_min', 'salary_max', 'salary_text',
        'band', 'profession_id', 'specialty_id', 'contract_type', 'employment_type',
        'sponsorship_information', 'requirements', 'qualifications', 'experience_required',
        'closing_date', 'published_date', 'source_url', 'raw_reference', 'status',
        'classification_method', 'classification_confidence',
        'first_seen_at', 'last_updated_at', 'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'status'                    => JobStatus::class,
            'salary_min'                => 'float',
            'salary_max'                => 'float',
            'classification_confidence' => 'float',
            'closing_date'              => 'date',
            'published_date'            => 'date',
            'first_seen_at'             => 'datetime',
            'last_updated_at'           => 'datetime',
            'last_seen_at'              => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(JobSource::class, 'source_id');
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function locationModel(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(JobRequirement::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(JobSkill::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(JobMatch::class);
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedJob::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            JobStatus::Active->value,
            JobStatus::Updated->value,
            JobStatus::ClosingSoon->value,
        ]);
    }

    public function scopeVisible($query)
    {
        return $query->active()
            ->where(function ($q) {
                // Exclude jobs with a past closing date
                $q->whereNull('closing_date')
                  ->orWhere('closing_date', '>=', now()->toDateString());
            });
    }
}
