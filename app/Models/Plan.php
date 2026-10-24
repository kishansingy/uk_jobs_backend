<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price_monthly', 'price_annual',
        'currency', 'provider_monthly_id', 'provider_annual_id', 'active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'float',
            'price_annual'  => 'float',
            'active'        => 'boolean',
        ];
    }

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function hasFeature(string $key): bool
    {
        return $this->features()->where('feature_key', $key)->exists();
    }

    public function getFeatureValue(string $key): ?string
    {
        return $this->features()->where('feature_key', $key)->value('feature_value');
    }
}
