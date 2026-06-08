<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Frequence extends Model
{
    protected $table = 'frequences';

    protected $fillable = ['spam_rule_id', 'frequency_limit', 'frequency_period'];

    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }
}
