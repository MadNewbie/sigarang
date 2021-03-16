<?php

namespace App\Http\Controllers\Sigarang\Goods;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Goods\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use function view;

class UnitController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "goods";
    protected static $modelName = "unit";
    
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
        
        $unitTableName = Unit::getTableName();
        
        $q = Unit::query()
            ->select([
                "{$unitTableName}.name",
                "{$unitTableName}.id",
            ]);
        
            Helper::fluentMultiSearch($q, $search, [
                "{$unitTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Unit $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Unit $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        return self::makeView('create');
    }
    
    public function store(Request $request)
    {
        $unitTableName = Unit::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$unitTableName},name",
        ]);
        
        $res = true;
        $input = $request->all();
        $unit = new Unit();
        $unit->fill($input);
        $res &= $unit->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Unit create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $unit->errors)));
        }
    }
    
    public function edit($id)
    {
        $unit = Unit::find($id);
        $options = compact('unit');
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ["required"],
        ]);
        
        $res = true;
        $input = $request->all();
        $unit = Unit::find($id);
        $unit->fill($input);
        $res &= $unit->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Unit create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $unit->errors)));
        }
    }
    
    public function show($id)
    {
        $unit = Unit::find($id);
        
        return self::makeView('show', compact('unit'));
    }
    
    public function destroy($id)
    {
        /* @var $model Unit */
        $model = Unit::find($id);
        if(count($model->goods)!=0){
            return 'Data has been used';
        }
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
}

