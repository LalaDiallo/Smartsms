<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'reference',
        'subscription_id',
        'client_id',
        'user_id',
        'payment_method',
        'provider_name',
        'provider_transaction_id',
        'amount',
        'currency',
        'status',
        'phone',
        'payment_url',
        'provider_response',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'paid_at'           => 'datetime',
        'amount'            => 'decimal:2',
    ];

    public static function generateReference(): string
    {
        return 'PAY-' . strtoupper(uniqid());
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
