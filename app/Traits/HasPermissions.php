<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasPermissions
{
    public function hasPermission($permission)
    {
        return DB::table('roles_permissions')
            ->where('role', $this->role)
            ->value($permission) ?? false;
    }

    public function getAllPermissions()
    {
        $permissions = DB::table('roles_permissions')
            ->where('role', $this->role)
            ->first();

        if (!$permissions) {
            return [];
        }

        $result = [];
        foreach ($permissions as $key => $value) {
            if (str_starts_with($key, 'peut_') && $value) {
                $result[] = $key;
            }
        }

        return $result;
    }
}
