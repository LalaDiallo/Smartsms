<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'slug',
        'price_monthly_base',
        'sms_included_monthly',
        'sms_price_reference',
        'rollover_months',
        'features',
        'is_freemium',
        'has_long_term_discounts',
        'status',
    ];

    protected $casts = [
        'features' => 'array',
        'is_freemium' => 'boolean',
        'has_long_term_discounts' => 'boolean',
    ];

    /* =====================
     | RELATIONS
     ===================== */

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function extraSmsPricing()
    {
        return $this->hasMany(ExtraSmsPricing::class);
    }

    /* =====================
     | SCOPES
     ===================== */

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('is_freemium', false);
    }

    /* =====================
     | BUSINESS LOGIC
     ===================== */

    public function isFreemium(): bool
    {
        return $this->is_freemium === true;
    }

    public function smsIncludedForCycle(BillingCycle $cycle): int
    {
        $bonus = ($this->sms_included_monthly * $cycle->sms_bonus_percent) / 100;

        return (int) round(
            ($this->sms_included_monthly * $cycle->months) + $bonus
        );
    }

    public function basePriceForCycle(BillingCycle $cycle): int
    {
        $price = $this->price_monthly_base * $cycle->months;

        if ($this->has_long_term_discounts) {
            $price -= ($price * $cycle->discount_percent) / 100;
        }

        return (int) round($price);
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }
}
