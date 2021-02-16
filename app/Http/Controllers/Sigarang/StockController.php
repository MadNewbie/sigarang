<?php

namespace App\Http\Controllers\Sigarang;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Stock;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Auth;

class StockController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = null;
    protected static $modelName = "stock";
    
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
        
        $stockTableName = Stock::getTableName();
        $marketTableName = Market::getTableName();
        $goodsTableName = Goods::getTableName();
        $userTableName = "users";
        
        $q = Stock::query()
            ->select([
                "{$stockTableName}.date",
                "{$userTableName}.name as pic",
                "{$marketTableName}.name as market_name",
                "{$goodsTableName}.name as goods_name",
                "{$stockTableName}.stock",
                "{$stockTableName}.id",
                "{$stockTableName}.goods_id",
                "{$stockTableName}.market_id",
            ])
            ->leftJoin($marketTableName, "{$stockTableName}.market_id", "{$marketTableName}.id")
            ->leftJoin($userTableName, "{$stockTableName}.created_by", "{$userTableName}.id")
            ->leftJoin($goodsTableName, "{$stockTableName}.goods_id", "{$goodsTableName}.id");
        
            Helper::fluentMultiSearch($q, $search, [
                "{$goodsTableName}.name",
                "{$marketTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('date', function(Stock $v) {
                return date("d F Y",strtotime($v->date));
            })
            ->editColumn('market_name', function(Stock $v) {
                return '<a href="' . route('backyard.area.market.show',$v->id) .'">' . $v->market_name . '</a>';
            })
            ->editColumn('goods_name', function(Stock $v) {
                return '<a href="' . route('backyard.goods.goods.show',$v->id) .'">' . $v->goods_name . '</a>';
            })
            ->editColumn('_menu', function(Stock $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['market_name', 'goods_name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
     public function edit($id)
    {
        $model = Stock::find($id);
        $options = compact('model');
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'date' => ["required"],
            'stock' => ["required"],
        ]);
        
        $res = true;
        $input = $request->all();
        $model = Stock::find($id);
        $model->fill($input);
        $res &= $model->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Data update successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $model->errors)));
        }
    }
    
    public function destroy($id)
    {
        $model = Stock::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
}

