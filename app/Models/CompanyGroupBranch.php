<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyGroupBranch extends Model
{
    protected $fillable = [
        'group_id', 'client_id', 'zone_name',
        'zone_type', 'sms_quota_allocated', 'status',
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
