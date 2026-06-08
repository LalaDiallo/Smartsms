<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
   protected $fillable = ['name', 'description',
        'client_id',
        'name',
        'type',        // static | dynamic
        'description',
        'created_by',];

    public function contacts()
    {
        return $this->belongsToMany(Contacts::class, 'groupe_contacts', 'groupe_id', 'contact_id')
                    ->withTimestamps(); // optionnel : pour gérer created_at / updated_at dans la pivot
    }

    public function rules()
    {
        return $this->hasMany(SegmentRule::class);
    }

    public function audits()
    {
        return $this->hasMany(SegmentAudit::class);
    }
}

