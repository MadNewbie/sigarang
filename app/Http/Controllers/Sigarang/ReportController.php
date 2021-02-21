<?php

namespace App\Http\Controllers\Sigarang;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Libraries\OpenTBS;
use App\Lookups\Sigarang\PriceLookup;
use App\Lookups\Sigarang\StockLookup;
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
        $this->middleware('permission:' . self::getRoutePrefix('download.price.index'), ['only' => ['reportPriceIndex']]);
        $this->middleware('permission:' . self::getRoutePrefix('download.price.download'), ['only' => ['reportPricePost']]);
        $this->middleware('permission:' . self::getRoutePrefix('download.stock.index'), ['only' => ['reportStockIndex']]);
        $this->middleware('permission:' . self::getRoutePrefix('download.stock.download'), ['only' => ['reportStockPost']]);
    }
    
    private function _getOptions($flag = null)
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
            $latestPrice = Price::select(['price'])->where('goods_id', '=', $item['id'])->latest()->first();
            $latestStock = Stock::select(['stock'])->where('goods_id', '=', $item['id'])->latest()->first();
            if(isset($flag) && strcmp($flag, 'price') == 0){
                if($latestPrice){
                    $item['latest_price'] = number_format($latestPrice->price,0,",",".");
                }else {
                    $item['latest_price'] = 0;
                }
            }
            if(isset($flag) && strcmp($flag, 'stock') == 0){
                if($latestStock){
                    $item['latest_stock'] = number_format($latestStock->stock,0,",",".");
                }else{
                    $item['latest_stock'] = 0;
                }
            }
            $categories[$item['category_id']]['goods'][] = $item;
        }
        
        return compact([
            'marketOptions',
            'categories',
        ]);
    }
    
    public function createDailyPrice()
    {
        $options = $this->_getOptions('price');
        return self::makeView('daily_price_create', $options);
    }
    
    public function createDailyStock()
    {
        $options = $this->_getOptions('stock');
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
                $inputPrice = [];
                $inputPrice['date'] = $input['date'];
                $inputPrice['market_id'] = $input['market_id'];
                $inputPrice['goods_id'] = $key;
                $inputPrice['price'] = filter_var($value,FILTER_SANITIZE_NUMBER_INT);
                $price = new Price();
                $price->fill($inputPrice);
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
                $price->stock = filter_var($value,FILTER_SANITIZE_NUMBER_INT);
                $price->created_by = Auth::user()->id;
                $price->updated_by = Auth::user()->id;
                $price->save();
            }
        }
        return redirect()->route(self::getRoutePrefix('daily.stock.create'))
                ->with("success", "Data create successfully");
    }
    
    public function reportPriceIndex()
    {
        $marketOptions = collect([null => "Pilih Pasar"]+Helper::createSelect(Market::orderBy("name")->get(), "name"));
        $todayDate = date('d-m-Y');
        $options = compact([
            'marketOptions',
            'todayDate',
        ]);
        return self::makeView('report_price_index', $options);
    }
    
    public function reportPricePost(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = date('Y-m-d',strtotime($request->get('start_date')));
        $endDate = date('Y-m-d',strtotime($request->get('end_date') . "+1 days"));
        $marketId = $request->get('market_id');
        
        $a = [];
        $b = [];
        $d = [
            'market_id' => Market::find($marketId)->name,
            'month' => date('M', strtotime($endDate)),
        ];
        
        $filters = [
            $startDate,
            $endDate,
        ];
        
        $priceTableName = Price::getTableName();
        $goodsTableName = Goods::getTableName();
        $unitTableName = Unit::getTableName();
        
        $priceQuery = Price::query()
            ->select([
                "{$priceTableName}.goods_id",
                "{$priceTableName}.price",
                "{$priceTableName}.created_at",
            ])
            ->whereBetween("{$priceTableName}.created_at",$filters)
            ->where("{$priceTableName}.type_status", PriceLookup::TYPE_STATUS_APPROVED)
            ->where("{$priceTableName}.market_id", $marketId);
        
        $goods = collect(Goods::query()
            ->select([
                "{$goodsTableName}.id",
                "{$goodsTableName}.name",
                "{$unitTableName}.name as unit_name",
            ])
            ->leftJoin($unitTableName, "{$goodsTableName}.unit_id", "{$unitTableName}.id")
            ->get())
            ->keyBy("id");
            
        $prices = $priceQuery->get();
        
        
        foreach($goods as $key => $value){
            $a[$key] = [
                "name" => $value->name,
                "unit" => $value->unit_name,
                "data" => []
            ];
        }
        
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        /*Formatting Data*/
        for($i = $startDate; $i < $endDate; $i->modify('+1day'))
        {
            $b[] = [
                'title' => $i->format('d')
            ];
            $date = $i->format('Y-m-d');
            foreach ($a as $key=>$value) {
                $a[$key]['data'][$i->format('d')] = 0; 
            }
            foreach ($prices as $price) {
                if(strcmp($price->created_at->format('Y-m-d'), $date)==0){
                    $a[$price->goods_id]['data'][$i->format('d')] = $price->price;
                }
            }
        }
        /* Calculate Average */
        foreach ($a as $key=>$value) {
            $sum = 0;
            $avg = 0;
            $counter = count($a[$key]['data']);
            foreach($a[$key]['data'] as $data){
                $sum += $data;
                if($data==0){
                    $counter--;
                };
            }
            if($sum > 0){
                $avg = $sum/$counter;            
            } else {
                $avg = 0;
            }
            $a[$key]['data']['Rata-rata'] = $avg;
        }
        $b[] = [
            "title"=>"Rata-rata",
        ];
        
        $path = dirname(__DIR__,4) . "/resources/views/backyard/sigarang/report/price_report_template.xlsx";
        $tbs = OpenTBS::loadTemplate($path);
        $tbs->mergeBlock('b1,b2', $b);
        $tbs->mergeBlock('a', $a);
        $tbs->mergeField('d', $d);
        $filename = sprintf('Laporan Dinamika Harga %s Bulan %s', $d['market_id'], $d['month']);
        $tbs->download("{$filename}.xlsx");
    }
    
    public function reportStockIndex()
    {
        $marketOptions = collect([null => "Pilih Pasar"]+Helper::createSelect(Market::orderBy("name")->get(), "name"));
        $todayDate = date('d-m-Y');
        $options = compact([
            'marketOptions',
            'todayDate',
        ]);
        return self::makeView('report_stock_index', $options);
    }
    
    public function reportStockPost(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = date('Y-m-d',strtotime($request->get('start_date')));
        $endDate = date('Y-m-d',strtotime($request->get('end_date') . "+1 days"));
        $marketId = $request->get('market_id');
        
        $a = [];
        $b = [];
        $d = [
            'market_id' => Market::find($marketId)->name,
            'month' => date('M', strtotime($endDate)),
        ];
        
        $filters = [
            $startDate,
            $endDate,
        ];
        
        $stockTableName = Stock::getTableName();
        $goodsTableName = Goods::getTableName();
        $unitTableName = Unit::getTableName();
        
        $stockQuery = Stock::query()
            ->select([
                "{$stockTableName}.goods_id",
                "{$stockTableName}.stock",
                "{$stockTableName}.created_at",
            ])
            ->whereBetween("{$stockTableName}.created_at",$filters)
            ->where("{$stockTableName}.type_status", StockLookup::TYPE_STATUS_APPROVED)
            ->where("{$stockTableName}.market_id", $marketId);
        
        $goods = collect(Goods::query()
            ->select([
                "{$goodsTableName}.id",
                "{$goodsTableName}.name",
                "{$unitTableName}.name as unit_name",
            ])
            ->leftJoin($unitTableName, "{$goodsTableName}.unit_id", "{$unitTableName}.id")
            ->get())
            ->keyBy("id");
            
        $stocks = $stockQuery->get();
        
        
        foreach($goods as $key => $value){
            $a[$key] = [
                "name" => $value->name,
                "unit" => $value->unit_name,
                "data" => []
            ];
        }
        
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);
        /*Formatting Data*/
        for($i = $startDate; $i < $endDate; $i->modify('+1day'))
        {
            $b[] = [
                'title' => $i->format('d')
            ];
            $date = $i->format('Y-m-d');
            foreach ($a as $key=>$value) {
                $a[$key]['data'][$i->format('d')] = 0; 
            }
            foreach ($stocks as $stock) {
                if(strcmp($stock->created_at->format('Y-m-d'), $date)==0){
                    $a[$stock->goods_id]['data'][$i->format('d')] = $stock->stock;
                }
            }
        }
//        dd($a);
        /*Checking Previous Value if not 0*/
        foreach ($a as $key => $value) {
            foreach($a[$key]['data'] as $index=>$val){
                if($index != date('d',strtotime($request->get('start_date')))){
                    if($val==0){
                        $a[$key]['data'][$index] = $a[$key]['data'][$index-1];
                    }
                }
            }
        }
        
        $path = dirname(__DIR__,4) . "/resources/views/backyard/sigarang/report/stock_report_template.xlsx";
        $tbs = OpenTBS::loadTemplate($path);
        $tbs->mergeBlock('b1,b2', $b);
        $tbs->mergeBlock('a', $a);
        $tbs->mergeField('d', $d);
        $filename = sprintf('Laporan Dinamika Stok %s Bulan %s', $d['market_id'], $d['month']);
        $tbs->download("{$filename}.xlsx");
    }
}

