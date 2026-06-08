<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'amount',
        'currency',
        'status',
        'created_at',
    ];

    public function contact()
    {
        return $this->belongsTo(Contacts::class);
    }

}
