<?php

namespace App\Http\Controllers\Sigarang\Area;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\Province;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;

class ProvinceController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "area";
    protected static $modelName = "province";
    
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
        
        $provinceTableName = Province::getTableName();
        
        $q = Province::query()
            ->select([
                "{$provinceTableName}.name",
                "{$provinceTableName}.id",
            ]);
        
            Helper::fluentMultiSearch($q, $search, [
                "{$provinceTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Province $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Province $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        /* @var $model Province */
        $model = new Province();
        
        $options = compact(['model']);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $provinceTableName = Province::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$provinceTableName},name",
        ]);
        
        $res = true;
        $input = $request->all();
        $province = new Province();
        $province->fill($input);
        $province->created_by = Auth::user()->id;
        $province->updated_by = Auth::user()->id;
        $res &= $province->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Province create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $province->errors)));
        }
    }
    
    public function edit($id)
    {
        /* @var $model Province */
        $model = Province::find($id);
        
        $options = compact(['model']);
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ["required"],
        ]);
        
        $res = true;
        $input = $request->all();
        $province = Province::find($id);
        $province->fill($input);
        $province->updated_by = Auth::user()->id;
        
        $res &= $province->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Province create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $province->errors)));
        }
    }
    
    public function show($id)
    {
        $model = Province::find($id);
        
        return self::makeView('show', compact('model'));
    }
    
    public function destroy($id)
    {
        $res = true;
        /* @var $model Province */
        $model = Province::find($id);
        if(count($model->cities)!=0){
            return 'Data has been used';
        }
        if($res){
            $res &= $model->delete();
        }
        return $res ? '1' : 'Data cannot be deleted';
    }
}

