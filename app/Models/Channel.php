<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = [
        'spam_rule_id',
        'channel', // sms | email | whatsapp | push
    ];

    public function spamRule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }
}
