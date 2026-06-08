<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamReports extends Model
{
    protected $fillable = [
        'type', 'content', 'sender', 'recipient', 'reason', 'status', 'timestamp', 'risk_score', 'rule_id'
    ];

    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'rule_id', 'id');
    }
}
