<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacts extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'client_id', 'zone_id', 'first_name', 'last_name', 'email', 'phone','status','region',
        'preferred_channel', 'is_spammer','gender','age','language','country',
    ];

    public function client()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function messages()
    {
        return $this->hasMany(Messages::class, 'contact_id');
    }
    public function responses()
    {
        return $this->hasMany(Responses::class, 'contact_id');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'groupe_contacts', 'contact_id', 'groupe_id');
    }

        public function groups()
    {
        return $this->belongsToMany(Groupe::class, 'groupe_contacts');
    }

    public function events()
    {
        return $this->hasMany(Events::class);
    }

    public function deliveryPreference()
    {
        return $this->hasOne(DeliveryPreference::class);
    }

    public function score()
    {
        return $this->hasOne(ContactScore::class);
    }
}
