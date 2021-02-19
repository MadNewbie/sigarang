<?php

namespace App\Http\Controllers\Sigarang;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Libraries\OpenTBS;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Category;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use App\Models\Sigarang\Stock;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Response;
use Yajra\DataTables\DataTables;

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
        $this->middleware('permission:' . self::getRoutePrefix('import.index'), ['only' => ['importCreate']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.store'), ['only' => ['importStore']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.download.template'), ['only' => ['importDownloadTemplate']]);
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
    
    public function importCreate()
    {
        return self::makeView('import');
    }
    
    public function importDownloadTemplate()
    {
        $goodsTableName = Goods::getTableName();
        $unitsTableName = Unit::getTableName();
        $goods = Goods::query()
            ->select([
                "{$goodsTableName}.name",
                "{$unitsTableName}.name as unit",
            ])
            ->leftJoin($unitsTableName, "{$goodsTableName}.unit_id", "{$unitsTableName}.id")
            ->get();
        $d = [];
        $a = [];
        $d['data_count'] = count($goods);
        foreach($goods as $good) {
            $a[] = [
                'name' => $good['name'],
                'unit' => $good['unit'],
            ];
        }
        $path = dirname(__DIR__,4) . "/resources/views/backyard/sigarang/stock/daily_stock_report_template.xlsx";
        $tbs = OpenTBS::loadTemplate($path);
        $tbs->mergeBlock('a', $a);
        $tbs->mergeField('d', $d);
        $filename = sprintf('Template Unggah Data Stok Harian');
        $tbs->download("{$filename}.xlsx");
    }
    
    public function importStore(Request $request)
    {
        set_time_limit(0);
        
        $files = $request->file('files');
        $result = [];
        $res = true;
        
        foreach ($files as $file) {
            $result = (object) [
                'file' => $file->getClientOriginalName(),
            ];
            
            $results[] = $result;
            
            if (!preg_match('/(spreadsheet|application\/CDFV2|application\/vnd.ms-excel)/', $file->getMimeType())) {
                $result->error = "Wrong Type Of File";
                continue;
            }
            $obj = IOFactory::load($file->getPathname());
            $sheet = $obj->getActiveSheet();
            
            $errors = [];

            $fileds = [
                'Nama Barang',
                'Satuan',
                'Stok',
            ];


            $row = 5;
            foreach ($fileds as $col => $name) {
                $header = $sheet->getCellByColumnAndRow($col + 1, $row)->getValue();
                if (trim(strtolower($header)) != trim(strtolower($name))) {
                    $errors[] = sprintf('Header mapping failed, expected: %s found: %s', $name, $header);
                }
            }

            if ($errors) {
                $result->error = implode('<br />', $errors);
                continue;
            }
            
            
            /*
             * Proses
             */
            $rowStart = 6;
            $rawRowMax = ($sheet->getCellByColumnAndRow(2,4)->getValue() instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) ? $sheet->getCellByColumnAndRow(2,4)->getValue()->getRichTextElements()[0]->getText() : $sheet->getCellByColumnAndRow(2,4)->getValue();
            $rowMax = $rowStart + $rawRowMax -1;
            
            $successCount = 0;
            $insertedCount = 0;
            
            DB::beginTransaction();
            $messages = [];
            
            for ($row = $rowStart; $row <= $rowMax; $row++) {
                $inputStock = [];
                $marketName = $sheet->getCellByColumnAndRow(2,3)->getValue();
                if(!isset($marketName)){
                    $errors[] = sprintf('Nama pasar belum terisi.');
                    $res = false;
                    break;
                }
                $marketLower = strtolower($marketName);
                $market = Market::whereRaw("LOWER(`name`) LIKE '%{$marketLower}%'")->first();
                if(!isset($market)){
                    $errors[] = sprintf('Data %s tidak ada dalam database pasar saat ini.', $market->name);
                    $res = false;
                    break;
                }
                $inputStock['goods_id'] = ($sheet->getCellByColumnAndRow(1,$row)->getValue() instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) ? $sheet->getCellByColumnAndRow(1,$row)->getValue()->getRichTextElements()[0]->getText() : $sheet->getCellByColumnAndRow(1,$row)->getValue();
                $inputStock['market_id'] = $market->id;
                $inputStock['stock'] = $sheet->getCellByColumnAndRow(3,$row)->getValue();
                $inputStock['date'] = date('Y-m-d');
                $goodsLower = strtolower($inputStock['goods_id']);
                $goods = Goods::whereRaw("LOWER(`name`) LIKE '%{$goodsLower}%'")->first();
                if($goods){
                    $inputStock['goods_id'] = $goods->id;
                } else {
                    $errors[] = sprintf('Data %s tidak ada dalam database barang saat ini.', $inputStock['goods_id']);
                    $res = false;
                    continue;
                }
                $stock = Stock::where([
                    'goods_id' => $inputStock['goods_id'],
                    'date' => $inputStock['date'],
                    'market_id' => $inputStock['market_id']
                ])->first() ? Stock::where([
                    'goods_id' => $inputStock['goods_id'],
                    'date' => $inputStock['date'],
                    'market_id' => $inputStock['market_id']
                ])->first() : new Stock();
                $stock->fill($inputStock);
                if(!isset($stock->stock)){
                    $oldStock = Stock::where([
                        "goods_id" => $stock->goods_id,
                        "market_id" => $stock->market_id,
                    ])->orderBy("date","DESC")->first();
                    $stock->stock= isset($oldStock->stock) ? $oldStock->stock : 0;
                }
                if(!$stock->save()){
                    $res = false;
                    $errors[] = sprintf('Proses menyimpan data stok barang %s gagal', $goods->name);
                    continue;
                } else {
                    $insertedCount++;
                    $successCount++;                        
                }
            }
            $messages[] = sprintf('%s data stok barang berhasil diupload.', number_format($successCount,0,".",""));
            $messages[] = sprintf('%s data stok barang berhasil ditambahkan.', number_format($insertedCount,0,".",""));
            $result->message = implode('<br />', $messages);
            $result->error = implode('<br />', $errors);
            $res ? DB::commit() : DB::rollBack();
            
        }
        
        return Response::json($results);
    }
}

