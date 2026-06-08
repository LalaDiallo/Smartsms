<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SegmentRule extends Model
{
    protected $fillable = [
        'groupe_id',
        'field',
        'operator',
        'value',
        'logical',
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
