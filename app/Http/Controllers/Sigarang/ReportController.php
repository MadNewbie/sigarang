<?php

namespace App\Http\Controllers\Sigarang;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Category;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use App\Models\Sigarang\Price;
use App\Models\Sigarang\Stock;
use Auth;
use Illuminate\Http\Request;

class ReportController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = null;
    protected static $modelName = "report";
    
    public function __construct()
    {
        $this->middleware('permission:' . self::getRoutePrefix('daily.price.create'), ['only' => ['createDailyPrice']]);
        $this->middleware('permission:' . self::getRoutePrefix('daily.price.store'), ['only' => ['storeDailyPrice']]);
        $this->middleware('permission:' . self::getRoutePrefix('daily.stock.create'), ['only' => ['createDailyStock']]);
        $this->middleware('permission:' . self::getRoutePrefix('daily.stock.store'), ['only' => ['storeDailyStock']]);
    }
    
    private function _getOptions()
    {
        $goodsTableName = Goods::getTableName();
        $unitsTableName = Unit::getTableName();
        $marketOptions = collect([null => "Pilih Pasar"] + Helper::createSelect(Market::orderBy('name')->get(), "name"));
        $categories = Category::all()->keyBy('id')->toArray();
        $goods = Goods::query()
            ->select([
                "{$goodsTableName}.id",
                "{$goodsTableName}.name",
                "{$goodsTableName}.category_id",
                "{$unitsTableName}.name as unit_name",
            ])
            ->leftJoin($unitsTableName, "{$goodsTableName}.unit_id", "{$unitsTableName}.id")
            ->get()
            ->toArray();
        
        foreach($goods as $item){
            $categories[$item['category_id']]['goods'][] = $item;
        }
        
        return compact([
            'marketOptions',
            'categories',
        ]);
    }
    
    public function createDailyPrice()
    {
        $options = $this->_getOptions();
        return self::makeView('daily_price_create', $options);
    }
    
    public function createDailyStock()
    {
        $options = $this->_getOptions();
        return self::makeView('daily_stock_create', $options);
    }
    
    public function storeDailyPrice(Request $request)
    {
        $this->validate($request, [
            "market_id" => "required",
        ]);
        $input = $request->all();
        $input['date'] = date("Y-m-d");
        foreach($input['goods'] as $key=>$value){
            if(isset($value)){
                /* @var $price Price */
                $price = new Price();
                $price->market_id = $input['market_id'];
                $price->date = $input['date'];
                $price->goods_id = $key;
                $price->price = $value;
                $price->created_by = Auth::user()->id;
                $price->updated_by = Auth::user()->id;
                $price->save();
            }
        }
        return redirect()->route(self::getRoutePrefix('daily.price.create'))
                ->with("success", "Data create successfully");
    }
    
    public function storeDailyStock(Request $request)
    {
        $this->validate($request, [
            "market_id" => "required",
        ]);
        $input = $request->all();
        $input['date'] = date("Y-m-d");
        foreach($input['goods'] as $key=>$value){
            if(isset($value)){
                /* @var $price Stock */
                $price = new Stock();
                $price->market_id = $input['market_id'];
                $price->date = $input['date'];
                $price->goods_id = $key;
                $price->stock = $value;
                $price->created_by = Auth::user()->id;
                $price->updated_by = Auth::user()->id;
                $price->save();
            }
        }
        return redirect()->route(self::getRoutePrefix('daily.stock.create'))
                ->with("success", "Data create successfully");
    }
}

