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
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Response;

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
        $this->middleware('permission:' . self::getRoutePrefix('download.price.download.pdf'), ['only' => ['reportPricePostPdf']]);
        $this->middleware('permission:' . self::getRoutePrefix('download.stock.index'), ['only' => ['reportStockIndex']]);
        $this->middleware('permission:' . self::getRoutePrefix('download.stock.download'), ['only' => ['reportStockPost']]);
        $this->middleware('permission:' . self::getRoutePrefix('download.stock.download.pdf'), ['only' => ['reportStockPostPdf']]);
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
            $categories[$item['category_id']]['goods'][$item['id']] = $item;
        }

        return compact([
            'marketOptions',
            'categories',
            'flag',
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

    protected function getData($startDate, $endDate, $marketId, $goodIds)
    {
        $res = [];

        $res['a'] = [];
        $res['b'] = [];
        $res['d'] = [
            'market_id' => Market::find($marketId)->name,
            'start_date' => date('d F Y', strtotime($startDate)),
            'end_date' => date('d F Y', strtotime($endDate)),
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
                "{$priceTableName}.date",
            ])
            ->whereBetween("{$priceTableName}.date",$filters)
            ->where("{$priceTableName}.type_status", PriceLookup::TYPE_STATUS_APPROVED)
            ->where("{$priceTableName}.market_id", $marketId)
            ->whereIn("{$priceTableName}.goods_id", $goodIds);

        $goods = collect(Goods::query()
            ->select([
                "{$goodsTableName}.id",
                "{$goodsTableName}.name",
                "{$unitTableName}.name as unit_name",
            ])
            ->leftJoin($unitTableName, "{$goodsTableName}.unit_id", "{$unitTableName}.id")
            ->whereIn("{$goodsTableName}.id", $goodIds)
            ->get())
            ->keyBy("id");

        $prices = $priceQuery->get();


        foreach($goods as $key => $value){
            $res['a'][$key] = [
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
            $res['b'][] = [
                'title' => $i->format('d')
            ];
            $date = $i->format('Y-m-d');
            foreach ($res['a'] as $key=>$value) {
                $res['a'][$key]['data'][$i->format('d')] = 0;
            }
            foreach ($prices as $price) {
                if(strcmp(date('Y-m-d', strtotime($price->date)), $date)==0){
                    $res['a'][$price->goods_id]['data'][$i->format('d')] = $price->price;
                }
            }
        }
        /* Calculate Average */
        foreach ($res['a'] as $key=>$value) {
            $sum = 0;
            $avg = 0;
            $counter = count($res['a'][$key]['data']);
            foreach($res['a'][$key]['data'] as $data){
                $sum += $data;
                if($data==0){
                    $counter--;
                };
            }
            if($sum > 0){
                $avg = round($sum/$counter,0);
            } else {
                $avg = 0;
            }
            $res['a'][$key]['data']['Rata-rata'] = round($avg,0);
        }
        $res['b'][] = [
            "title"=>"Rata-rata",
        ];

        return $res;
    }

    public function reportPriceIndex()
    {
        $marketOptions = collect([null => "Pilih Pasar"]+Helper::createSelect(Market::orderBy("name")->get(), "name"));
        $goods = Goods::all();
        $todayDate = date('d-m-Y');
        $options = compact([
            'marketOptions',
            'todayDate',
            'goods',
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
        $goodIds = $request->get('goods');

        $data = $this->getData($startDate, $endDate, $marketId, $goodIds);

        $path = dirname(__DIR__,4) . "/resources/views/backyard/sigarang/report/price_report_template.xlsx";
        $tbs = OpenTBS::loadTemplate($path);
        $tbs->mergeBlock('b1,b2', $data['b']);
        $tbs->mergeBlock('a', $data['a']);
        $tbs->mergeField('d', $data['d']);
        $filename = sprintf('Laporan Dinamika Harga %s Periode %s - %s', $data['d']['market_id'], $data['d']['start_date'], $data['d']['end_date']);
        $tbs->download("{$filename}.xlsx");
    }

    public function reportPricePostPdf(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = date('Y-m-d',strtotime($request->get('start_date')));
        $endDate = date('Y-m-d',strtotime($request->get('end_date') . "+1 days"));
        $marketId = $request->get('market_id');
        $goodIds = $request->get('goods');

        $data = $this->getData($startDate, $endDate, $marketId, $goodIds);

        $pdf = App::make('dompdf.wrapper');
        // $pdf->loadView('backyard.sigarang.report.template_price_pdf', compact("data"));
        $pdf = $pdf->loadHTML(self::makeView('template_price_pdf',compact("data"))->render());
        return $pdf->stream();
    }

    public function reportStockIndex()
    {
        $marketOptions = collect([null => "Pilih Pasar"]+Helper::createSelect(Market::orderBy("name")->get(), "name"));
        $goods = Goods::all();
        $todayDate = date('d-m-Y');
        $options = compact([
            'marketOptions',
            'todayDate',
            'goods',
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
        $goodIds = $request->get('goods');

        $data = $this->getData($startDate, $endDate, $marketId, $goodIds);

        $path = dirname(__DIR__,4) . "/resources/views/backyard/sigarang/report/stock_report_template.xlsx";
        $tbs = OpenTBS::loadTemplate($path);
        $tbs->mergeBlock('b1,b2', $data['b']);
        $tbs->mergeBlock('a', $data['a']);
        $tbs->mergeField('d', $data['d']);
        $filename = sprintf('Laporan Dinamika Stok %s Periode %s - %s', $data['d']['market_id'], $data['d']['start_date'], $data['d']['end_date']);
        $tbs->download("{$filename}.xlsx");
    }

    public function reportStockPostPdf(Request $request)
    {
        $this->validate($request, [
            'market_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = date('Y-m-d',strtotime($request->get('start_date')));
        $endDate = date('Y-m-d',strtotime($request->get('end_date') . "+1 days"));
        $marketId = $request->get('market_id');
        $goodIds = $request->get('goods');

        $data = $this->getData($startDate, $endDate, $marketId, $goodIds);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('backyard.sigarang.report.template_stock_pdf', $data);
        return $pdf->stream();
    }

    public function postPricePlaceholder(Request $request){
        $market_id = $request->get('id_pasar');
        $goodsTableName = Goods::getTableName();
        $unitsTableName = Unit::getTableName();

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
            $latestPrice = Price::select(['price'])->where(['goods_id' => $item['id'], 'market_id' => $market_id])->latest()->first();
            if($latestPrice){
                $item['latest_price'] = number_format($latestPrice->price,0,",",".");
            }else {
                $item['latest_price'] = 0;
            }
            $categories[$item['category_id']]['goods'][$item['id']] = $item;
        }

        return Response::json($categories);
    }

    public function postStockPlaceholder(Request $request){
        $market_id = $request->get('id_pasar');
        $goodsTableName = Goods::getTableName();
        $unitsTableName = Unit::getTableName();

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
            $latestStock = Stock::select(['stock'])->where(['goods_id' => $item['id'], 'market_id' => $market_id])->latest()->first();
            if($latestStock){
                $item['latest_stock'] = number_format($latestStock->stock,0,",",".");
            }else{
                $item['latest_stock'] = 0;
            }
            $categories[$item['category_id']]['goods'][$item['id']] = $item;
        }

        return Response::json($categories);
    }
}

