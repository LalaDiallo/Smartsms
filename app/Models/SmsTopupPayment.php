<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTopupPayment extends Model
{
    protected $fillable = [
        'client_id', 'subscription_id', 'sms_count',
        'price', 'currency', 'payment_ref', 'status',
    ];

    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function client()       { return $this->belongsTo(Clients::class); }
}
