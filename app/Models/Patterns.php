<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patterns extends Model
{
    protected $table = 'patterns';

    protected $fillable = ['pattern','spam_rule_id'];

    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }
}
