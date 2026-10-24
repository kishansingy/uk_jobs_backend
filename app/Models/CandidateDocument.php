<?php

namespace App\Models;

use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'type', 'original_filename', 'storage_path',
        'disk', 'mime_type', 'file_size', 'is_current', 'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'type'        => DocumentType::class,
            'is_current'  => 'boolean',
            'uploaded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(CvAnalysis::class, 'document_id');
    }
}
