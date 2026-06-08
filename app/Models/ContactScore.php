<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactScore extends Model
{
    protected $fillable = [
        'contact_id',
        'engagement_score',
        'risk_level',
        'last_activity_at',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
    ];

    public function contact()
    {
        return $this->belongsTo(Contacts::class);
    }
}
