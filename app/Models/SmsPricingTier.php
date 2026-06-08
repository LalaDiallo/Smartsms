<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'min_volume',
        'max_volume',
        'price_per_sms',
        'currency',
    ];

    // 🔗 Relation
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
