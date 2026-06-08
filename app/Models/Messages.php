<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Messages extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'campagnes_id',
        'contact_id',
        'content',
        'sent_at',
        'status',
        'subject',
        'media',
        'cta',
        'cta_url',
        'channel',
        'reply_token'
    ];

    public function campaign()
    {
        return $this->belongsTo(Campagnes::class, 'campagnes_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contacts::class, 'contact_id');
    }
    public function responses()
    {
        return $this->hasMany(Responses::class);
    }
}
