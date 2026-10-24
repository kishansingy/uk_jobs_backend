<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewQuestion extends Model
{
    protected $fillable = ['category_id', 'profession_id', 'question', 'guidance', 'difficulty', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InterviewCategory::class, 'category_id');
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(InterviewAnswer::class, 'question_id');
    }
}
