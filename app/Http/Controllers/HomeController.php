<?php

namespace App\Http\Controllers;

use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\District;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Price;
use Illuminate\Http\Request;
use Response;

class HomeController extends Controller
{    
    public function landingPage()
    {
        $marketSelect = Helper::createSelect(Market::orderBy('name')->get(), 'name');
        $goodsSelect = Helper::createSelect(Goods::orderBy('name')->get(), 'name');
        $options = compact(['marketSelect', 'goodsSelect']);
        return view('forecourt.landing_page', $options);
    }
    
    public function getMapData(Request $request)
    {
        $goods_id = $request->get('goods_id');
        $date = $request->get('date');
        $date = date("Y-m-d", strtotime($date));
        
        $priceTableName = Price::getTableName();
        $marketTableName = Market::getTableName();
        $districtTableName = District::getTableName();
        $districtAreaTableName = \App\Models\Sigarang\Area\DistrictArea::getTableName();
        
        $districtData = District::query()
            ->select([
                "{$districtTableName}.id",
                "{$districtAreaTableName}.area as district_area",
                "{$districtTableName}.name",
            ])
            ->leftJoin($districtAreaTableName,"{$districtAreaTableName}.district_id","{$districtTableName}.id")
            ->get()
            ->keyBy("id");
        
        $averagePriceData = Price::query()
            ->select([
                "{$priceTableName}.price",
            ])
            ->where([
                "goods_id" => $goods_id,
                "date" => $date,
                "type_status" => \App\Lookups\Sigarang\PriceLookup::TYPE_STATUS_APPROVED,
            ])
            ->avg("price");
        
        $priceData = Price::query()
            ->select([
                "{$marketTableName}.district_id",
                "{$priceTableName}.price",
            ])
            ->where([
                "goods_id" => $goods_id,
                "date" => $date,
                "type_status" => \App\Lookups\Sigarang\PriceLookup::TYPE_STATUS_APPROVED,
            ])
            ->leftJoin($marketTableName, "{$priceTableName}.market_id", "{$marketTableName}.id")
            ->get();
        $res = [
            'avgPrice' => $averagePriceData ? : 0,
        ];
        $rawData = [];
        foreach ($districtData as $district) {
            /* @var $district District */
            $rawData[$district['id']] = [
                'id' => $district['id'],
                'area' => $district->area->getPoint(),
                'name' => $district['name'],
                'price' => 0,
                'count' => 0,
            ];
        }
        $priceData->keyBy('district_id');
        if(count($priceData) != 0){
            foreach ($priceData as $data) {
                $rawData[$data['district_id']]['price'] += $data['price'];
                $rawData[$data['district_id']]['count']++;
            }
        }
        $formattedData = [
            "type"=> "FeatureCollection",
            "features"=> [],
        ];
        
        foreach($rawData as $data){
            $formattedData["features"][] = [
                "type" => "Feature",
                "properties" => [
                    "id" => $data['id'],
                    "name" => $data['name'],
                    "price" => $data['price'] == 0 ? 0 : number_format($data['price'] / $data['count'], 0, '', '.'),
                    "fillColor" => $this->getAreaColor($data, $res['avgPrice']),
                    "note" => $this->getNote($data, $res['avgPrice']),
                ],
                "geometry" => json_decode($data['area']),
            ];
        }
        
        $res["dataPrice"] = $formattedData;
        $res['avgPrice'] = $averagePriceData ? number_format($averagePriceData, 0, "", ".") : 0;
        
        $result = [
            'status' => true,
            'data' => $res,
        ];
        
        return Response::json($result);
    }
    
    public function getAreaColor($data, $avgPrice) {
        $color = '';
        if($data['price'] == 0){
            $color = 'grey';
        } else if(((($data['price'] / $data['count']) - $avgPrice) / $avgPrice) * 100 <= -10){
            $color = 'yellow';
        }else if(((($data['price'] / $data['count']) - $avgPrice) / $avgPrice) * 100 >= 10){
            $color = 'red';
        } else if(((($data['price'] / $data['count']) - $avgPrice) / $avgPrice) * 100 < 10 || ((($data['price'] / $data['count']) - $avgPrice) / $avgPrice) * 100 > -10){
            $color = 'green';
        }
        return $color;
    }
    
    public function getNote($data, $avgPrice) {
        $diff = $data['count'] > 0 ? ($data['price'] / $data['count']) - $avgPrice : 0;
        $formatted_diff = number_format(abs($diff), 0, '', '.');
        $note = '';
        if($data['price'] == 0 || $data['count'] == 0){
            $note = '-';
        } else if($diff == 0){
            $note = 'Harga sama dengan harga rata-rata kabupaten';
        }else if($diff > 0){
            $note = "Harga selisih Rp.{$formatted_diff} lebih tinggi dari harga rata-rata kabupaten";
        } else if($diff < 0){
            $note = "Harga selisih Rp.{$formatted_diff} lebih rendah dari harga rata-rata kabupaten";
        }
        return $note;
    }
    
    public function getGraphData(Request $request)
    {
        $market_id = $request->get('market_id');
        $date = $request->get('date');
        $date = date("Y-m-d", strtotime($date));
        
        $startDate = date("Y-m-d", strtotime($date . "-10day"));
        $daterange = [$startDate, $date];
        
        $priceTableName = Price::getTableName();
        $goodsTableName = Goods::getTableName();
        $unitTableName = \App\Models\Sigarang\Goods\Unit::getTableName();
        
        $rawMasterData = Goods::query()
            ->select([
                "{$goodsTableName}.id",
                "{$goodsTableName}.name as goods_name",
                "{$unitTableName}.name as unit_name",
            ])
            ->leftJoin($unitTableName, "{$goodsTableName}.unit_id", "{$unitTableName}.id")
            ->get();
            
        $rawGraphData = Price::query()
            ->select([
                "{$priceTableName}.goods_id",
                "{$priceTableName}.price",
                "{$priceTableName}.date",
            ])
            ->where([
                "market_id" => $market_id,
                "type_status" => \App\Lookups\Sigarang\PriceLookup::TYPE_STATUS_APPROVED,
            ])
            ->whereBetween("date", $daterange)
            ->get()
            ->toArray();
                
        $formattedStartDate = new \DateTime($startDate);
        $formattedEndDate = new \DateTime($date);
        $masterData = [];
        foreach ($rawMasterData as $data) {  
            $masterData[$data['id']] = [
                'name' => $data['goods_name'],
                'unit' => $data['unit_name'],
                'curr_price' => 0,
                'hist_price' => [],
            ];
        }
        for ($i=$formattedStartDate; $i<=$formattedEndDate; $i->modify("+1day")) {
            foreach ($masterData as $key=>$master) {
                $notFound = true;
                foreach($rawGraphData as $graphData){
                    if ($graphData['goods_id']==$key) {
                        if (strcmp($i->format("Y-m-d"), $graphData['date']) == 0) {
                            array_push($masterData[$key]['hist_price'], $graphData['price']);
                            $notFound = false;
                        }
                    }
                }
                if($notFound){
                    array_push($masterData[$key]['hist_price'], 0);
                }
            }
        }
        foreach ($masterData as $key=>$data) {
            $masterData[$key]['curr_price'] = "Rp.".number_format($masterData[$key]['hist_price'][count($masterData[$key]['hist_price']) - 1], 0 , "", ".");
            $masterData[$key]['diff_last_price'] = $masterData[$key]['hist_price'][count($data['hist_price'])-1] - $masterData[$key]['hist_price'][count($data['hist_price'])-2];
            $masterData[$key]['diff_percentage'] = 0;
            if($masterData[$key]['hist_price'][count($masterData[$key]['hist_price']) - 1] > 0){
                $masterData[$key]['diff_percentage'] = number_format(abs(($masterData[$key]['diff_last_price'] / $masterData[$key]['hist_price'][count($masterData[$key]['hist_price']) - 1])*100), 2, ",", "");
            }
            if($masterData[$key]['diff_last_price'] > 0) {
                $masterData[$key]['status'] = 'Naik';
            }
            if($masterData[$key]['diff_last_price'] < 0) {
                $masterData[$key]['status'] = 'Turun';
            }
            if($masterData[$key]['diff_last_price'] == 0) {
                $masterData[$key]['status'] = 'Tetap';
            }
        }
        $result = [
            "status" => true,
            "data" => $masterData,
        ];
        return Response::json($result);
    }
}
