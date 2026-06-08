<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingCycle extends Model
{
    use HasFactory;

    protected $table = 'billing_cycles';

    protected $fillable = [
        'name',
        'months',
        'discount_percent',
        'sms_bonus_percent',
        'priority_support',
        'advanced_reports',
        'premium_features',
    ];

    protected $casts = [
        'priority_support' => 'boolean',
        'advanced_reports' => 'boolean',
        'premium_features' => 'boolean',
    ];

    /* =====================
     | RELATIONS
     ===================== */

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
