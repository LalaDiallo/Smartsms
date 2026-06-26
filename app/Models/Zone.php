<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'client_id', 'name', 'type', 'status',
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function campagnes()
    {
        return $this->hasMany(Campagnes::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contacts::class);
    }
}
