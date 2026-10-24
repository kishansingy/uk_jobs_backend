<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = ['name', 'slug', 'region', 'country', 'postcode_prefix', 'latitude', 'longitude', 'active'];

    protected function casts(): array
    {
        return [
            'active'    => 'boolean',
            'latitude'  => 'float',
            'longitude' => 'float',
        ];
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
