<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operators extends Model
{
    protected $fillable = [
        'name',
        'country',
        'cost_per_sms',
        'reliability_score',
    ];
}
