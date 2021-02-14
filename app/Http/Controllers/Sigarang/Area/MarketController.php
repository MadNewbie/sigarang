<?php

namespace App\Http\Controllers\Sigarang\Area;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Area\Province;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;

class MarketController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "area";
    protected static $modelName = "market";
    
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
    
    private function _getOptions($model)
    {
        $provinceOptions = collect([null => "Pilih Provinsi"] + Helper::createSelect(Province::orderBy("name")->get(), "name"));
        
        $options = compact([
            'model',
            'provinceOptions',
        ]);
        return $options;
    }
    
    public function index()
    {
        return self::makeView('index');
    }
    
    public function indexData(Request $request)
    {
        $search = $request->get('search')['value'];
        
        $marketTableName = Market::getTableName();
        
        $q = Market::query()
            ->select([
                "{$marketTableName}.name",
                "{$marketTableName}.id",
            ]);
        
            Helper::fluentMultiSearch($q, $search, [
                "{$marketTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Market $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Market $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        /* @var $model Market */
        $model = new Market();
        
        $options = $this->_getOptions($model);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $marketTableName = Market::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$marketTableName},name",
        ]);
        
        $res = true;
        $input = $request->all();
        $market = new Market();
        $market->fill($input);
        $market->created_by = Auth::user()->id;
        $market->updated_by = Auth::user()->id;
        $res &= $market->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Market create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $market->errors)));
        }
    }
    
    public function edit($id)
    {
        /* @var $model Market */
        $model = Market::find($id);
        
        $options = $this->_getOptions($model);
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ["required"],
        ]);
        
        $res = true;
        $input = $request->all();
        $market = Market::find($id);
        $market->fill($input);
        $market->updated_by = Auth::user()->id;
        
        $res &= $market->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Market create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $market->errors)));
        }
    }
    
    public function show($id)
    {
        $model = Market::find($id);
        
        return self::makeView('show', compact('model'));
    }
    
    public function destroy($id)
    {
        $model = Market::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
}

