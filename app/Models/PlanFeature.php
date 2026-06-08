<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'feature',
        'is_active',
    ];

    // 🔗 Relation
    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
