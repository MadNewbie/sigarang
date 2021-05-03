<?php

namespace App\Http\Controllers\User;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\User\User;
use Arr;
use DB;
use Hash;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;
use Auth;

class UserController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "user";
    protected static $submoduleName = null;
    protected static $modelName = "user";

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
        return self::makeView('index');
    }

    public function indexData(Request $request)
    {
        $search = $request->get('search')['value'];

        $userTableName = 'users';

        $q = User::query()
            ->select([
                "{$userTableName}.name",
                "{$userTableName}.id",
            ]);

        Helper::fluentMultiSearch($q, $search, [
            "{$userTableName}.name",
        ]);

        $res = DataTables::of($q)
            ->editColumn('name', function(User $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(User $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);

        return $res;
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return self::makeView('create', compact('roles'));
    }

    public function store(Request $request)
    {
        $photo = $request->file('photo');
        $options = compact(['photo']);
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        /* @var $user User */
        $user = new User();
        $user->fill($input);
        $user->saveWithDetails($options);
        $user->assignRole($request->input('roles'));

        return redirect()->route(self::getRoutePrefix('index'))
            ->with('success', 'User create successfully');
    }

    public function show($id)
    {
        $user = User::find($id);
        return self::makeView('show', compact('user'));
    }

    public function edit($id)
    {
        // if(Auth::user()->id!=$id){
        //     return redirect()->route(self::getRoutePrefix('index'))
        //         ->with('error','Tidak bisa mengedit user tersebut');
        // }
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();

        return self::makeView('edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        if(strtolower(Auth::user()->roles[0]->name) != "admin" && strtolower(Auth::user()->roles[0]->name) != "developer"){
            if(Auth::user()->id!=$id){
                return redirect()->route(self::getRoutePrefix('index'))
                    ->with('error','Tidak bisa mengedit user tersebut');
            }
        }
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
        ];

        $this->validate($request, $rules);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = \Illuminate\Support\Facades\Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password', 'confirm-password'));
        }

        $user = User::find($id);
        $user->update($input);
        if($request->get('roles')){
            DB::table('model_has_roles')->where('model_id', $id)->delete();

            $user->assignRole($request->input('roles'));
        }

        return redirect()->route(self::getRoutePrefix('index'))
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $model = User::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';;
    }
}

