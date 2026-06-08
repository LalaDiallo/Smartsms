<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insights extends Model
{
    protected $fillable = [
        'user_id',
        'insight_type',
        'data',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedDataAttribute()
    {
        return json_decode($this->data, true);
    }
}
