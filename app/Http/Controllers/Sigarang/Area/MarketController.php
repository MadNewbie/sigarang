<?php

namespace App\Http\Controllers\Sigarang\Area;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\City;
use App\Models\Sigarang\Area\District;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Area\MarketPoint;
use App\Models\Sigarang\Area\Province;
use Auth;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Response;
use Yajra\DataTables\DataTables;

class MarketController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "area";
    protected static $modelName = "market";
    
    public function __construct()
    {
        $this->middleware('permission:' . self::getRoutePrefix('index'), ['only' => ['index','indexData']]);
        $this->middleware('permission:' . self::getRoutePrefix('show'), ['only' => ['show']]);
        $this->middleware('permission:' . self::getRoutePrefix('create'), ['only' => ['create']]);
        $this->middleware('permission:' . self::getRoutePrefix('store'), ['only' => ['store']]);
        $this->middleware('permission:' . self::getRoutePrefix('edit'), ['only' => ['edit']]);
        $this->middleware('permission:' . self::getRoutePrefix('update'), ['only' => ['update']]);
        $this->middleware('permission:' . self::getRoutePrefix('destroy'), ['only' => ['destroy']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.index'), ['only' => ['importCreate']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.store'), ['only' => ['importStore']]);
        $this->middleware('permission:' . self::getRoutePrefix('import.download.template'), ['only' => ['importDownloadTemplate']]);
    }
    
    private function _getOptions($model)
    {
        $provinceOptions = collect([null => "Pilih Provinsi"] + Helper::createSelect(Province::orderBy("name")->get(), "name"));
        
        $options = compact([
            'model',
            'provinceOptions',
        ]);
        return $options;
    }
    
    public function index()
    {
        return self::makeView('index');
    }
    
    public function indexData(Request $request)
    {
        $search = $request->get('search')['value'];
        
        $marketTableName = Market::getTableName();
        
        $q = Market::query()
            ->select([
                "{$marketTableName}.name",
                "{$marketTableName}.id",
            ]);
        
            Helper::fluentMultiSearch($q, $search, [
                "{$marketTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Market $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Market $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        /* @var $model Market */
        $model = new Market();
        
        $options = $this->_getOptions($model);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $marketTableName = Market::getTableName();
        ($this->validate($request, [
            'name' => "required|unique:{$marketTableName},name",
        ]));
        
        $res = true;
        $input = $request->all();
        $market = new Market();
        $market->fill($input);
        $market->created_by = Auth::user()->id;
        $market->updated_by = Auth::user()->id;
        $res &= $market->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Market create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $market->errors)));
        }
    }
    
    public function edit($id)
    {
        /* @var $model Market */
        $model = Market::find($id);
        
        $options = $this->_getOptions($model);
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ["required"],
        ]);
        
        $res = true;
        $input = $request->all();
        $market = Market::find($id);
        $market->fill($input);
        $market->updated_by = Auth::user()->id;
        
        $res &= $market->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Market create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $market->errors)));
        }
    }
    
    public function show($id)
    {
        /* @var $model Market */
        $model = Market::find($id);
        if($model->point){
            $model->point->getPoint();
        }
        
        return self::makeView('show', compact('model'));
    }
    
    public function destroy($id)
    {
        $model = Market::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
    
    public function importCreate()
    {
        return self::makeView('import');
    }
    
    public function importDownloadTemplate()
    {
        $path = resource_path('/views/backyard/sigarang/area/market/import_data_market_template.xlsx');
        return Response::download($path);
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
                'Nama Provinsi',
                'Nama Kab/Kota',
                'Nama Kecamatan',
                'Nama Pasar',
                'Titik Lokasi Pasar (Latitude; Longitude)',
            ];


            $row = 4;
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
            $rowStart = 5;
            $rowMax = $rowStart + $sheet->getCellByColumnAndRow(2,3)->getValue() -1;
            
            $successCount = 0;
            $updatedCount = 0;
            $insertedCount = 0;
            
            DB::beginTransaction();
            $messages = [];
            
            for ($row = $rowStart; $row <= $rowMax; $row++) {
                $inputProvince = [];
                $inputCity = [];
                $inputDistrict = [];
                $inputMarket = [];
                $inputProvince['name'] = $sheet->getCellByColumnAndRow(1,$row)->getValue();
                $inputCity['name'] = $sheet->getCellByColumnAndRow(2,$row)->getValue();
                $inputDistrict['name'] = $sheet->getCellByColumnAndRow(3,$row)->getValue();
                $inputMarket['name'] = $sheet->getCellByColumnAndRow(4,$row)->getValue();
                $provinceLower = strtolower($inputProvince['name']);
                $cityLower = strtolower($inputCity['name']);
                $districtLower = strtolower($inputDistrict['name']);
                $marketLower = strtolower($inputMarket['name']);
                $province = Province::whereRaw("LOWER(`name`) LIKE '%{$provinceLower}%'")->first();
                $city = City::whereRaw("LOWER(`name`) LIKE '%{$cityLower}%'")->first();
                $district = District::whereRaw("LOWER(`name`) LIKE '%{$districtLower}%'")->first();
                $market = Market::whereRaw("LOWER(`name`) LIKE '%{$marketLower}%'")->first();
                if (!isset($province)) {
                    $province = new Province();
                    $province->fill($inputProvince);
                    if(!$province->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Provinsi %s gagal', $inputProvince['name']);
                    }
                }
                if (!isset($city)) {
                    $city = new City();
                    $inputCity['province_id'] = $province->id;
                    $city->fill($inputCity);
                    if(!$city->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Kota / Kabupaten %s gagal', $inputCity['name']);
                    }
                }
                if (!isset($district)) {
                    $district = new District();
                    $inputDistrict['city_id'] = $city->id;
                    $district->fill($inputDistrict);
                    if(!$district->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Kecamatan %s gagal', $inputDistrict['name']);
                    }
                }
                if ($market) {
                    $inputMarket['district_id'] = $district->id;
                    $market->fill($inputMarket);
                    if(!$market->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Pasar %s gagal', $inputMarket['name']);
                    } else {
                        $updatedCount++;
                        $successCount++;                        
                    }
                } else {
                    $market = new Market();
                    $inputMarket['district_id'] = $district->id;
                    $market->fill($inputMarket);
                    if(!$market->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Pasar %s gagal', $inputMarket['name']);
                    } else {
                        $insertedCount++;
                        $successCount++;
                    }
                }
                if($sheet->getCellByColumnAndRow(5,$row)->getValue() != null){
                    $inputMarketPoint = [
                        'market_id' => $market->id,
                        'area' => $sheet->getCellByColumnAndRow(5,$row)
                    ];
                    /* @var $marketPoint MarketPoint */
                    $marketPoint = MarketPoint::where(['market_id' => $market->id])->first() ? : new MarketPoint();;
                    $marketPoint->fill($inputMarketPoint);
                    $marketPoint->saveWithGeoSpatials();
                };
            }
            $messages[] = sprintf('%s data pasar berhasil diupload.', number_format($successCount,0,".",""));
            $messages[] = sprintf('%s data pasar berhasil ditambahkan.', number_format($insertedCount,0,".",""));
            $messages[] = sprintf('%s data pasar berhasil diperbaharui.', number_format($updatedCount,0,".",""));
            $result->message = implode('<br />', $messages);
            $res ? DB::commit() : DB::rollBack();
            
        }
        
        return Response::json($results);
    }
}

