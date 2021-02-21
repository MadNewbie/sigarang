<?php

namespace App\Http\Controllers\Sigarang;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Libraries\OpenTBS;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Category;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use App\Models\Sigarang\Price;
use Auth;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\DataTables;
use DB;
use Response;

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
        $this->middleware('permission:' . self::getRoutePrefix('import.index'), ['only' => ['importCreate']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.store'), ['only' => ['importStore']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.download.template'), ['only' => ['importDownloadTemplate']]);
        $this->middleware('permission:' . self::getRoutePrefix('approve'), ['only' => ['approvingPrice']]);
        $this->middleware('permission:' . self::getRoutePrefix('not.approve'), ['only' => ['notApprovingPrice']]);
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
                "{$priceTableName}.type_status",
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
            ->editColumn('type_status', function(Price $v) {
                /* @var $v Price */
                return $v->getTypeStatusBadge();
            })
            ->editColumn('_menu', function(Price $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['market_name', 'goods_name', 'type_status', '_menu'])
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
            'date' => ["required"],
            'price' => ["required"],
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
    
    public function approvingPrice($id)
    {
        /* @var $model Price */
        $model = Price::find($id);
        if($model->isTypeStatusApproved()){
            return back()->withErrors("Data harga {$model->goods->name} di pasar {$model->market->name} pada tanggal {$model->getFormattedDate()} telah berstatus disetujui");
        }
        if($model->approve()){
            return back()->with("success", "Perubahan status data harga berhasil disimpan");
        }else{
            return back()->with("error", "Perubahan status data harga gagal disimpan");
        }
    }
    
    public function notApprovingPrice($id)
    {
        /* @var $model Price */
        $model = Price::find($id);
        if($model->isTypeStatusNotApproved()){
            return back()->withErrors("Data harga {$model->goods->name} di pasar {$model->market->name} pada tanggal {$model->getFormattedDate()} telah berstatus tidak disetujui");
        }
        if($model->notApprove()){
            return back()->with("success", "Perubahan status data harga berhasil disimpan");
        }else{
            return back()->with("error", "Perubahan status data harga gagal disimpan");
        }
    }
    
    /*Upload Bulk*/
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
        $path = dirname(__DIR__,4) . "/resources/views/backyard/sigarang/price/daily_price_report_template.xlsx";
        $tbs = OpenTBS::loadTemplate($path);
        $tbs->mergeBlock('a', $a);
        $tbs->mergeField('d', $d);
        $filename = sprintf('Template Unggah Data Harga Harian');
        $tbs->download("{$filename}.xlsx");
    }

    public function importCreate()
    {
        return self::makeView('import');
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
                'Harga',
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
                $inputPrice = [];
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
                $inputPrice['goods_id'] = ($sheet->getCellByColumnAndRow(1,$row)->getValue() instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) ? $sheet->getCellByColumnAndRow(1,$row)->getValue()->getRichTextElements()[0]->getText() : $sheet->getCellByColumnAndRow(1,$row)->getValue();
                $inputPrice['market_id'] = $market->id;
                $inputPrice['price'] = $sheet->getCellByColumnAndRow(3,$row)->getValue();
                $inputPrice['date'] = date('Y-m-d');
                $goodsLower = strtolower($inputPrice['goods_id']);
                $goods = Goods::whereRaw("LOWER(`name`) LIKE '%{$goodsLower}%'")->first();
                if($goods){
                    $inputPrice['goods_id'] = $goods->id;
                } else {
                    $errors[] = sprintf('Data %s tidak ada dalam database barang saat ini.', $inputPrice['goods_id']);
                    $res = false;
                    continue;
                }
                $price = Price::where([
                    'goods_id' => $inputPrice['goods_id'],
                    'date' => $inputPrice['date'],
                    'market_id' => $inputPrice['market_id']
                ])->first() ? Price::where([
                    'goods_id' => $inputPrice['goods_id'],
                    'date' => $inputPrice['date'],
                    'market_id' => $inputPrice['market_id']
                ])->first() : new Price();
                $price->fill($inputPrice);
                if(!isset($price->price)){
                    $oldPrice = Price::where([
                        "goods_id" => $price->goods_id,
                        "market_id" => $price->market_id,
                    ])->orderBy("date","DESC")->first();
                    $price->price = isset($oldPrice->price) ? $oldPrice->price : 0;
                }
                if(!$price->save()){
                    $res = false;
                    $errors[] = sprintf('Proses menyimpan data harga barang %s gagal', $goods->name);
                    continue;
                } else {
                    $insertedCount++;
                    $successCount++;                        
                }
            }
            $messages[] = sprintf('%s data harga barang berhasil diupload.', number_format($successCount,0,".",""));
            $messages[] = sprintf('%s data harga barang berhasil ditambahkan.', number_format($insertedCount,0,".",""));
            $result->message = implode('<br />', $messages);
            $result->error = implode('<br />', $errors);
            $res ? DB::commit() : DB::rollBack();
            
        }
        
        return Response::json($results);
    }
}

