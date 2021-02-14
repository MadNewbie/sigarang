<?php

namespace App\Models\User;

use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    public static function updatePermission()
    {
        $excludeList = [
            'ignition',
            'login',
            'logout',
            'register',
            'home',
            'password.reset',
        ];
        $routes = \Route::getRoutes();
        $routeList = [];
        $excludeList = implode("|", $excludeList);
        foreach ($routes as $value) {
            $route = $value->getAction();
            if(isset($route['as']) && !preg_match("/^({$excludeList})/", $route['as'])){
                $routeList[] = $route['as'];
            }
        }
        $ids=[];
        foreach ($routeList as $value) {
            $permission = BasePermission::where(['name' => $value])->first();
            if($permission){
                $ids[] = $permission['id'];
            } else {
                $permission = BasePermission::create(['name' => $value]);
                $ids[] = $permission['id'];
            }
        }
        $permissions = BasePermission::whereNotIn('id',$ids)->get();
        foreach ($permissions as $permission) {
            $permission->delete();
        }
    }
}

