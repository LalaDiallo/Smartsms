<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpamKeywords extends Model
{
    protected $fillable = [
        'keyword',
        'spam_rule_id',
        'category',
        'created_at',
    ];

    public function getFormattedKeywordAttribute()
    {
        return strtoupper($this->keyword);
    }

    public function rule()
    {
        return $this->belongsTo(SpamRules::class, 'spam_rule_id');
    }
}
