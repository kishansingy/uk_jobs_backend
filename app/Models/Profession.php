<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profession extends Model
{
    protected $fillable = ['name', 'slug', 'registration_body', 'description', 'active', 'sort_order'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function candidateProfiles(): HasMany
    {
        return $this->hasMany(CandidateProfile::class);
    }
}
