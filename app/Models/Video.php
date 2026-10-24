<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contributor_id', 'category_id', 'title', 'description', 'video_url',
        'thumbnail_url', 'duration_seconds', 'status', 'premium_only',
        'approved_by', 'published_at', 'view_count',
    ];

    protected function casts(): array
    {
        return [
            'premium_only' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(Contributor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VideoCategory::class, 'category_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(VideoTag::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }
}
