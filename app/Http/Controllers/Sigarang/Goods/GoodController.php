<?php

namespace App\Http\Controllers\Sigarang\Goods;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Goods\Category;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;

class GoodController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "goods";
    protected static $modelName = "goods";
    
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
        
        $categoryTableName = Category::getTableName();
        $unitTableName = Unit::getTableName();
        $goodTableName = Goods::getTableName();
        
        $q = Goods::query()
            ->select([
                "{$categoryTableName}.name as category_name",
                "{$goodTableName}.name",
                "{$unitTableName}.name as unit_name",
                "{$goodTableName}.id",
                "{$goodTableName}.unit_id",
                "{$goodTableName}.category_id",
            ])
            ->leftJoin($categoryTableName, "{$goodTableName}.category_id", "{$categoryTableName}.id")
            ->leftJoin($unitTableName, "{$goodTableName}.unit_id", "{$unitTableName}.id");
        
            Helper::fluentMultiSearch($q, $search, [
                "{$goodTableName}.name",
                "{$categoryTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Goods $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Goods $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    private function _getOptions($model)
    {
        $categoryTableName = Category::getTableName();
        $unitTableName = Unit::getTableName();
        
        $categoryOptions = collect([null => 'Pilih Kategori'] + Helper::createSelect(Category::orderBy('name')->get(), 'name'));
        $unitOptions = collect([null => 'Pilih Satuan'] + Helper::createSelect(Unit::orderBy('name')->get(), 'name'));
        $options = compact([
            'model',
            'categoryOptions',
            'unitOptions',
        ]);
        return $options;
    }
    
    public function create()
    {
        /* @var $model Goods */
        $model = new Goods();
        
        $options = $this->_getOptions($model);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $res = true;
        $goodsTableName = Goods::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$goodsTableName},name",
            'unit_id' => "required",
            'category_id' => "required",
        ]);
        $input = $request->all();
        /* @var $model Goods */
        $model = new Goods();
        
        $model->fill($input);
        $model->created_by = Auth::user()->id;
        $model->edited_by = Auth::user()->id;
        
        $res &= $model->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Goods create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $category->errors)));
        }
    }
    
    public function edit($id)
    {
         /* @var $model Goods */
        $model = Goods::find($id);
        
        $options = $this->_getOptions($model);
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $res = true;
        /* @var $model Goods */
        $model = Goods::find($id);
        
        $goodsTableName = Goods::getTableName();
        $this->validate($request, [
            'name' => "required",
            'unit_id' => "required",
            'category_id' => "required",
        ]);
        $input = $request->all();
        
        $model->fill($input);
        $model->edited_by = Auth::user()->id;
        
        $res &= $model->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Goods updated successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $category->errors)));
        }
    }
    
    public function show($id)
    {
        /* @var $model Goods */
        $model = Goods::find($id);
        
        $options = compact(['model']);
        return self::makeView('show', $options);
    }
    
    public function destroy($id)
    {
        $model = Goods::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
}

