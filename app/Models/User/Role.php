<?php

namespace App\Models\User;

use Spatie\Permission\Models\Role as BaseRole;

/**
 * @property string $id
 * @property string $name
 * @property string $guard_name
 */

class Role extends BaseRole
{
    public static function updateDeveloperPermissions()
    {
        $permissions = Permission::select(['id'])->get();
        $roleDeveloper = Role::where(['name' => 'Developer'])->first();
        $permissionIds = [];
        foreach($permissions as $permission){
            $permissionIds[] = $permission->id;
        }
        if($roleDeveloper){
            $roleDeveloper->syncPermissions($permissionIds);
        }
    }
}
