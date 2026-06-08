<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'currency',
        'included_sms_volume',
        'overuse_price_per_sms',
        'limitations',
        'is_active',
    ];

    protected $casts = [
        'price'                  => 'decimal:2',
        'overuse_price_per_sms' => 'decimal:2',
        'is_active'              => 'boolean',
    ];

    // Un plan a plusieurs clients
    public function clients(): HasMany
    {
        return $this->hasMany(Clients::class);
    }

    // Relations vers les tables associées
    public function pricingTiers(): HasMany
    {
        return $this->hasMany(SmsPricingTier::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }
}
