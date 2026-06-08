<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responses extends Model
{
    protected $fillable = [
        'message_id',
        'contact_id',
        'content',
        'received_at',
    ];

    public function message()
    {
        return $this->belongsTo(Messages::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contacts::class);
    }
}
