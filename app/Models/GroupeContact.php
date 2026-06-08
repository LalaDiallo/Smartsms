<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupeContact extends Model
{
    protected $fillable = ['groupe_id', 'contact_id'];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contacts::class, 'contact_id');
    }
}
