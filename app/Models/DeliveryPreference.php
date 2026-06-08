<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPreference extends Model
{
    protected $fillable = [
        'contact_id',
        'preferred_days',
        'preferred_hours',
    ];

    protected $casts = [
        'preferred_days'  => 'array',
        'preferred_hours' => 'array',
    ];

    public function contact()
    {
        return $this->belongsTo(Contacts::class);
    }
}
