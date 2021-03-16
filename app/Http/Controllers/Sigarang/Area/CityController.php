<?php

namespace App\Http\Controllers\Sigarang\Area;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\City;
use App\Models\Sigarang\Area\Province;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;
use Response;

class CityController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "area";
    protected static $modelName = "city";
    
    public function __construct()
    {
        $this->middleware('permission:' . self::getRoutePrefix('index'), ['only' => ['index','indexData']]);
        $this->middleware('permission:' . self::getRoutePrefix('show'), ['only' => ['show']]);
        $this->middleware('permission:' . self::getRoutePrefix('create'), ['only' => ['create']]);
        $this->middleware('permission:' . self::getRoutePrefix('store'), ['only' => ['store']]);
        $this->middleware('permission:' . self::getRoutePrefix('edit'), ['only' => ['edit']]);
        $this->middleware('permission:' . self::getRoutePrefix('update'), ['only' => ['update']]);
        $this->middleware('permission:' . self::getRoutePrefix('destroy'), ['only' => ['destroy']]);
        $this->middleware('permission:' . self::getRoutePrefix('ajax.get.city.by.province.id'), ['only' => ['ajaxGetCityByProvinceId']]);
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
        
        $cityTableName = City::getTableName();
        
        $q = City::query()
            ->select([
                "{$cityTableName}.name",
                "{$cityTableName}.id",
            ]);
        
            Helper::fluentMultiSearch($q, $search, [
                "{$cityTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(City $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(City $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        /* @var $model City */
        $model = new City;
        
        $options = $this->_getOptions($model);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $cityTableName = City::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$cityTableName},name",
        ]);
        
        $res = true;
        $input = $request->all();
        $city = new City();
        $city->fill($input);
        $city->created_by = Auth::user()->id;
        $city->updated_by = Auth::user()->id;
        $res &= $city->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "City create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $city->errors)));
        }
    }
    
    public function edit($id)
    {
        /* @var $model City */
        $model = City::find($id);
        
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
        $city = City::find($id);
        $city->fill($input);
        $city->updated_by = Auth::user()->id;
        
        $res &= $city->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "City create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $city->errors)));
        }
    }
    
    public function show($id)
    {
        $model = City::find($id);
        
        return self::makeView('show', compact('model'));
    }
    
    public function destroy($id)
    {
        $res = true;
        $model = City::find($id);
        /* @var $model District */
        if(count($model->districts)!=0){
            return 'Data has been used';
        }
        if($res){
            $res &= $model->delete();
        }
        return $res ? '1' : 'Data cannot be deleted';
    }
    
    public function ajaxGetCityByProvinceId($id)
    {
        $res = Helper::createSelect(City::where(['province_id' => $id])->orderBy("name")->get(), "name");
        return Response::json($res);
    }
}

