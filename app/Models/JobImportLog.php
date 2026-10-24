<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobImportLog extends Model
{
    protected $fillable = [
        'source_id', 'status', 'records_fetched', 'records_created',
        'records_updated', 'records_skipped', 'error_message',
        'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(JobSource::class, 'source_id');
    }
}
