<?php

namespace App\Http\Controllers\Sigarang;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Price;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;

class PriceController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = null;
    protected static $modelName = "price";
    
    public function __construct()
    {
        $this->middleware('permission:' . self::getRoutePrefix('index'), ['only' => ['index','indexData']]);
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
        
        $priceTableName = Price::getTableName();
        $marketTableName = Market::getTableName();
        $goodsTableName = Goods::getTableName();
        $userTableName = "users";
        
        $q = Price::query()
            ->select([
                "{$priceTableName}.date",
                "{$userTableName}.name as pic",
                "{$marketTableName}.name as market_name",
                "{$goodsTableName}.name as goods_name",
                "{$priceTableName}.price",
                "{$priceTableName}.id",
                "{$priceTableName}.goods_id",
                "{$priceTableName}.market_id",
                "{$priceTableName}.created_by",
            ])
            ->leftJoin($marketTableName, "{$priceTableName}.market_id", "{$marketTableName}.id")
            ->leftJoin($userTableName, "{$priceTableName}.created_by", "{$userTableName}.id")
            ->leftJoin($goodsTableName, "{$priceTableName}.goods_id", "{$goodsTableName}.id");
        
            Helper::fluentMultiSearch($q, $search, [
                "{$goodsTableName}.name",
                "{$marketTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('date', function(Price $v) {
                return date("d F Y",strtotime($v->date));
            })
            ->editColumn('market_name', function(Price $v) {
                return '<a href="' . route('backyard.area.market.show',$v->id) .'">' . $v->market_name . '</a>';
            })
            ->editColumn('goods_name', function(Price $v) {
                return '<a href="' . route('backyard.goods.goods.show',$v->id) .'">' . $v->goods_name . '</a>';
            })
            ->editColumn('_menu', function(Price $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['market_name', 'goods_name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
     public function edit($id)
    {
        $model = Price::find($id);
        $options = compact('model');
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ["required"],
        ]);
        
        $res = true;
        $input = $request->all();
        $model = Price::find($id);
        $model->fill($input);
        $model->updated_by = Auth::user()->id;
        $res &= $model->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Data create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $model->errors)));
        }
    }
    
    public function destroy($id)
    {
        $model = Price::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
}

