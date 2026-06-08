<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campagnes extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'region',
        'channel',
        'template_id',
        'settings',
        'archived',
    ];

    protected $casts = [
        'settings'  => 'array',
        'archived'  => 'boolean',
        'start_date'=> 'datetime',
        'end_date'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

    public function template()
    {
        return $this->belongsTo(Templates::class);
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, 'campagnes_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
