<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class contentRules extends Model
{
    protected $table = 'content_rules';

    protected $fillable = ['spam_rule_id','rule'];

    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }
}
