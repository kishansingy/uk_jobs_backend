<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'category_id');
    }
}
