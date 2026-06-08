<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'client_id', 'name', 'description', 'enabled'
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }
}
