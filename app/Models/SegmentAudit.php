<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentAudit extends Model
{
    protected $fillable = [
        'groupe_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
