<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyGroupBranch extends Model
{
    protected $fillable = [
        'group_id', 'client_id', 'zone_name',
        'zone_type', 'status',
        'invitation_token', 'invitation_expires_at',
    ];

    protected $casts = [
        'invitation_expires_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(CompanyGroup::class, 'group_id');
    }

    public function client()
    {
        return $this->belongsTo(Clients::class);
    }
}
