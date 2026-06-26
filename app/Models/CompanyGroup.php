<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyGroup extends Model
{
    protected $fillable = [
        'name', 'description', 'industry',
        'owner_client_id', 'logo', 'status',
    ];

    public function ownerClient()
    {
        return $this->belongsTo(Clients::class, 'owner_client_id');
    }

    public function branches()
    {
        return $this->hasMany(CompanyGroupBranch::class, 'group_id');
    }

    public function branchClients()
    {
        return $this->belongsToMany(Clients::class, 'company_group_branches', 'group_id', 'client_id')
            ->withPivot(['zone_name', 'zone_type', 'status'])
            ->withTimestamps();
    }
}
