<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamRules extends Model
{
    protected $fillable = [
        'nom_regle',
        'type',
        'action',
        'severity',
        'description',
        'status',
        'auto_learn',
    ];

    /* ============================
     | Relations
     |============================ */

    public function keywords()
    {
        return $this->hasMany(SpamKeywords::class, 'spam_rule_id');
    }

    public function patterns()
    {
        return $this->hasMany(Patterns::class, 'spam_rule_id');
    }

    public function frequence()
    {
        return $this->hasOne(Frequence::class, 'spam_rule_id');
    }

    public function senderDomains()
    {
        return $this->hasMany(SenderDomains::class, 'spam_rule_id');
    }

    public function contentRules()
    {
        return $this->hasMany(ContentRules::class, 'spam_rule_id');
    }

    public function channels()
    {
        return $this->hasMany(Channel::class, 'spam_rule_id');
    }
}
