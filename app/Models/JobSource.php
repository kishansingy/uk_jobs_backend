<?php

namespace App\Models;

use App\Enums\JobSourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobSource extends Model
{
    protected $fillable = [
        'name', 'type', 'base_url', 'source_identifier', 'profession_id', 'active', 'authorised',
        'fetch_method', 'fetch_interval_minutes', 'configuration', 'permission_status', 'terms_reference',
        'terms_last_checked_at', 'last_success_at', 'last_failure_at', 'last_failure_message',
    ];

    protected function casts(): array
    {
        return [
            'active'                => 'boolean',
            'authorised'            => 'boolean',
            'configuration'         => 'encrypted:array', // encrypts credentials at rest
            'type'                  => JobSourceType::class,
            'terms_last_checked_at' => 'datetime',
            'last_success_at'       => 'datetime',
            'last_failure_at'       => 'datetime',
        ];
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'source_id');
    }

    public function importLogs(): HasMany
    {
        return $this->hasMany(JobImportLog::class, 'source_id');
    }
}
