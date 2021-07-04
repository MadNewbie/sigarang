<?php

namespace App\Http\Controllers;

use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\District;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use App\Models\Sigarang\Price;
use DB;
use Response;
use Illuminate\Http\Request;

class BackyardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
//        $totalGoodsData = Goods::count();
//        $priceTableName = Price::getTableName();
//        $marketTableName = Market::getTableName();
//        $districtTableName = District::getTableName();
//        $dataAddedPerMarket = Price::query()
//            ->select([
//                "{$marketTableName}.name",
//                DB::raw("COUNT({$priceTableName}.market_id) AS total"),
//                "{$priceTableName}.market_id",
//                "{$marketTableName}.district_id",
//            ])
//            ->leftJoin($marketTableName, "{$priceTableName}.market_id", "{$marketTableName}.id")
//            ->where(['date' => date('Y-m-d')])->groupBy('market_id')
//            ->get();
//
//        $districts = District::query()
//            ->select([
//                "{$districtTableName}.name",
//                "{$districtTableName}.id",
//            ])
//            ->get();
//        foreach($districts as $district){
//            $district['completion_percentage'] = 0;
//            foreach($dataAddedPerMarket as $data){
//                if($district->id == $data->district_id){
//                    $district['completion_percentage'] += ($data->total / $totalGoodsData) * 100;
//                }
//            }
//            if ($district['completion_percentage'] <= 30) {
//                $district['color'] = 'red';
//            } elseif ($district['completion_percentage'] <= 50) {
//                $district['color'] = 'yellow';
//            } else {
//                $district['color'] = 'green';
//            }
//
//            /* @var $district District */
//            if($district->area){
//                $district['area'] = $district->area->getPoint();
//            }
//        }
        $marketSelect = Helper::createSelect(Market::orderBy('name')->get(), 'name');
        $goodsSelect = Helper::createSelect(Goods::orderBy('name')->get(), 'name');
        $options = compact(['marketSelect', 'goodsSelect']);
        return view('backyard.home_leaflet', $options);
    }

    /**
     * Format return
     *
     *  {
     *      label: "Harga Bawang Putih",
     *      data: [
     *          {
     *              x: moment("02/01/2021").format("DD-MM-YYYY"),
     *              y: 11000,
     *          },
     *          {
     *              x: moment("02/05/2021").format("DD-MM-YYYY"),
     *              y: 12000,
     *          },
     *          {
     *              x: moment("02/10/2021").format("DD-MM-YYYY"),
     *              y: 11500,
     *          },
     *          .
     *          .
     *          .
     *      ],
     *  }
     */

    public function getPriceGraphData(Request $request)
    {
        $market_id = $request->get('market_id');
        $goods_id = $request->get('goods_id');

        $goodsTableName = Goods::getTableName();
        $unitTableName = Unit::getTableName();
        $goodsData = Goods::query()
            ->select([
                "{$goodsTableName}.name",
                "{$unitTableName}.name as unit_name",
            ])
            ->leftJoin($unitTableName, "{$goodsTableName}.unit_id", "{$unitTableName}.id")
            ->where([
                "{$goodsTableName}.id" => $goods_id,
            ])
            ->first();

        $res = [
            "label" => "Harga {$goodsData->name}",
            "data" => [],
        ];

        $priceTableName = Price::getTableName();
        $priceData = Price::query()
            ->select([
                "{$priceTableName}.date",
                "{$priceTableName}.price",
            ])
            ->where([
                "market_id" => $market_id,
                "goods_id" => $goods_id,
            ])->get()
            ->keyBy("date");
        $rawStartDate = date("Y/m/01");
        $rawEndDate = date("Y/m/d", strtotime($rawStartDate . "+1 month -1 day"));
        $startDate = new \DateTime($rawStartDate);
        $endDate = new \DateTime($rawEndDate);

        for($i = $startDate; $i < $endDate; $i->modify('+1day')){
            $res["data"][] = (object) [
                "x" => "moment('{$i->format("d/m/Y")}').format('DD-MM-YYYY')",
                "y" => isset($priceData[$i->format("Y-m-d")]) ? $priceData[$i->format("Y-m-d")]->price : 0,
            ];
        }
        $res = (object) $res;
        return Response::json($res);
    }

    public function getStockGraphData(Request $request)
    {
        $market_id = $request->get('market_id');
        $goods_id = $request->get('goods_id');

        $goodsTableName = Goods::getTableName();
        $unitTableName = Unit::getTableName();
        $goodsData = Goods::query()
            ->select([
                "{$goodsTableName}.name",
                "{$unitTableName}.name as unit_name",
            ])
            ->leftJoin($unitTableName, "{$goodsTableName}.unit_id", "{$unitTableName}.id")
            ->where([
                "{$goodsTableName}.id" => $goods_id,
            ])
            ->first();

        $res = [
            "label" => "Stock {$goodsData->name} dalam satuan {$goodsData->unit_name}",
            "data" => [],
        ];

        $stockTableName = \App\Models\Sigarang\Stock::getTableName();
        $stockData = \App\Models\Sigarang\Stock::query()
            ->select([
                "{$stockTableName}.date",
                "{$stockTableName}.stock",
            ])
            ->where([
                "market_id" => $market_id,
                "goods_id" => $goods_id,
            ])->get()
            ->keyBy("date");
        $rawStartDate = date("Y/m/01");
        $rawEndDate = date("Y/m/d", strtotime($rawStartDate . "+1 month -1 day"));
        $startDate = new \DateTime($rawStartDate);
        $endDate = new \DateTime($rawEndDate);

        for($i = $startDate; $i < $endDate; $i->modify('+1day')){
            if(strcmp($i->format("Y-m-d"), date("Y-m-01")) != 0){
                $oldValue = $res["data"][count($res["data"]) - 1];
            }
            $res["data"][] = (object) [
                "x" => "moment('{$i->format("d/m/Y")}').format('DD-MM-YYYY')",
                "y" => isset($stockData[$i->format("Y-m-d")]) ? $stockData[$i->format("Y-m-d")]->stock : (isset($oldValue) ? $oldValue->y : 0),
            ];
        }
        $res = (object) $res;
        return Response::json($res);
    }

    public function getMapData(Request $request)
    {
        $date = $request->get('date');
        $date = date("Y-m-d", strtotime($date));

        $totalGoodsData = Goods::count();
        $priceTableName = Price::getTableName();
        $marketTableName = Market::getTableName();
        $districtTableName = District::getTableName();
        $dataAddedPerMarket = Price::query()
            ->select([
                "{$marketTableName}.name",
                DB::raw("COUNT({$priceTableName}.market_id) AS total"),
                "{$priceTableName}.market_id",
                "{$marketTableName}.district_id",
            ])
            ->leftJoin($marketTableName, "{$priceTableName}.market_id", "{$marketTableName}.id")
            ->where(['date' => $date])->groupBy([
                "{$priceTableName}.market_id",
                "{$marketTableName}.name",
                "{$marketTableName}.district_id",
            ])
            ->get();

        $districts = District::query()
            ->select([
                "{$districtTableName}.name",
                "{$districtTableName}.id",
            ])
            ->get();
        $formattedData = [
            "type"=> "FeatureCollection",
            "features"=> [],
        ];
        foreach($districts as $district){
            $tmpDistrict = [
                "type" => "Feature",
                "properties" => [
                    "id" => $district['id'],
                    "name" => $district['name'],
                    "completion_percentage" => 0,
                    "color" => "",
                ],
                "geometry" => null,
            ];
            foreach($dataAddedPerMarket as $data){
                if($tmpDistrict['properties']['id'] == $data->district_id){
                    $tmpDistrict['properties']['completion_percentage'] += ($data->total / $totalGoodsData) * 100;
                }
            }
            if ($tmpDistrict['properties']['completion_percentage'] <= 30) {
                $tmpDistrict['properties']['color'] = 'red';
            } elseif ($tmpDistrict['properties']['completion_percentage'] <= 50) {
                $tmpDistrict['properties']['color'] = 'yellow';
            } else {
                $tmpDistrict['properties']['color'] = 'green';
            }

            /* @var $district District */
            if($district->area){
                $tmpDistrict['geometry'] = json_decode($district->area->getPoint());
            }
            $formattedData['features'][] = $tmpDistrict;
        }
        return Response::json($formattedData);
    }
}

