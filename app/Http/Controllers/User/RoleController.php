<?php

namespace App\Http\Controllers\User;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\User\Permission;
use App\Models\User\Role as Role2;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use function view;

class RoleController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "user";
    protected static $submoduleName = null;
    protected static $modelName = "role";    
    
    public function __construct()
    {
        $this->middleware('permission:' . self::getRoutePrefix('index'), ['only' => ['index','indexData']]);
        $this->middleware('permission:' . self::getRoutePrefix('show'), ['only' => ['show']]);
        $this->middleware('permission:' . self::getRoutePrefix('create'), ['only' => ['create']]);
        $this->middleware('permission:' . self::getRoutePrefix('store'), ['only' => ['store']]);
        $this->middleware('permission:' . self::getRoutePrefix('edit'), ['only' => ['edit']]);
        $this->middleware('permission:' . self::getRoutePrefix('update'), ['only' => ['update']]);
        $this->middleware('permission:' . self::getRoutePrefix('destroy'), ['only' => ['destroy']]);
    }
    
    public function index()
    {
        Permission::updatePermission();
        Role2::updateDeveloperPermissions();
        return self::makeView('index');
    }
    
    public function indexData(Request $request)
    {
        $search = $request->get('search')['value'];
        
        $roleTableName = config('permission.table_names.roles');
        
        $q = Role::query()
            ->select([
                "{$roleTableName}.name",
                "{$roleTableName}.id",
            ]);
        
        Helper::fluentMultiSearch($q, $search, [
            "{$roleTableName}.name",
        ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Role $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Role $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        $permission = Permission::get();
        return self::makeView('create', compact('permission'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);
        
        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permissions'));
        
        return redirect()->route(self::getRoutePrefix('index'))
            ->with('success','Role created successfully');
    }
    
    public function show($id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $id)
            ->get();
        
        return self::makeView('show', compact('role','rolePermissions'));
    }
    
    public function edit($id)
    {
        $role = Role::find($id);
        $permission = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return self::makeView('edit', compact('role','permission','rolePermissions'));
    }
    
    public function update(Request $request, $id)
    {
        $res = $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required',
        ]);
        
        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();
        
        $role->syncPermissions($request->input('permissions'));
        
        return redirect()->route(self::getRoutePrefix('index'))
            ->with('success', 'Role updated successfully');
    }
    
    public function destroy($id)
    {
        $model = Role::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
}

