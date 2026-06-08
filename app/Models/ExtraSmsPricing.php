<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraSmsPricing extends Model
{
    use HasFactory;

    protected $table = 'extra_sms_pricing';

    protected $fillable = [
        'subscription_plan_id',
        'min_quantity',
        'max_quantity',
        'price_per_sms',
    ];

    /* =====================
     | RELATIONS
     ===================== */

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /* =====================
     | BUSINESS LOGIC
     ===================== */

    public function scopeForQuantity($query, int $quantity)
    {
        return $query
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($q) use ($quantity) {
                $q->whereNull('max_quantity')
                  ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('min_quantity', 'desc');
    }
}
