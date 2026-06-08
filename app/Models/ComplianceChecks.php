<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceChecks extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'regulation', 'status', 'last_check', 'score', 'issues'
    ];

    protected $casts = [
        'issues' => 'array',
    ];
}
