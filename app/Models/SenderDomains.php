<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SenderDomains extends Model
{
    protected $table = 'sender_domains';

    protected $fillable = ['spam_rule_id','domain'];

    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }
}
