<?php

namespace App\Http\Controllers\Sigarang\Area;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Area\City;
use App\Models\Sigarang\Area\District;
use App\Models\Sigarang\Area\DistrictArea;
use App\Models\Sigarang\Area\Province;
use Auth;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Response;
use Yajra\DataTables\DataTables;
use DB;

class DistrictController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "area";
    protected static $modelName = "district";
    
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
        
        $districtTableName = District::getTableName();
        $districtAreaTableName = DistrictArea::getTableName();
        
        $q = District::query()
            ->select([
                "{$districtTableName}.name",
                "{$districtTableName}.id",
            ]);
        
            Helper::fluentMultiSearch($q, $search, [
                "{$districtTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(District $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('area', function(District $v) {
                $area = DistrictArea::where(['district_id' => $v->id])->first();
                return $area ? '<span class="badge badge-success">Ada</span>': '<span class="badge badge-danger">Tidak Ada</span>';
            })
            ->editColumn('_menu', function(District $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', 'area', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    public function create()
    {
        /* @var $model District */
        $model = new District;
        
        $options = $this->_getOptions($model);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $districtTableName = District::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$districtTableName},name",
        ]);
        
        $res = true;
        $errors = [];
        $input = $request->all();
        $district = new District();
        $district->fill($input);
        $district->created_by = Auth::user()->id;
        $district->updated_by = Auth::user()->id;
        $res &= $district->save();
        
        $area = $request->get('area');
        if($area){
            $rawPoints = explode(";",$area);
            $formattedPoints = [];
            foreach ($rawPoints as $point) {
                $formattedPoints[] = $point;
            }
            /* cek polygon sudah tertutup atau belum */
            if(strcmp($formattedPoints[0], $formattedPoints[count($formattedPoints) - 1]) != 0){
                $errors[] = "Batas area belum tertutup";
                $res = false;
            }
            $formattedPoints = implode(",", $formattedPoints);
            $inputDistrictArea = [
                'district_id' => $district->id,
                'area' => $formattedPoints,
            ];
            /* @var $districtArea DistrictArea */
            $districtArea = DistrictArea::where(['district_id' => $district->id])->first() ? : new DistrictArea();
            $districtArea->fill($inputDistrictArea);
            if ($res) {
                $districtArea->saveWithGeoSpatials();
            }
        }
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "District create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $errors)));
        }
    }
    
    public function edit($id)
    {
        /* @var $model District */
        $model = District::find($id);
        
        $options = $this->_getOptions($model);
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ["required"],
        ]);
        
        $res = true;
        $errors = [];
        $input = $request->all();
        $district = District::find($id);
        $district->fill($input);
        $district->updated_by = Auth::user()->id;
        
        $res &= $district->save();
        
        $area = $request->get('area');
        if($area){
            $rawPoints = explode(";",$area);
            $formattedPoints = [];
            foreach ($rawPoints as $point) {
                $formattedPoints[] = $point;
            }
            /* cek polygon sudah tertutup atau belum */
            if(strcmp($formattedPoints[0], $formattedPoints[count($formattedPoints) - 1]) != 0){
                $errors[] = "Batas area belum tertutup";
                $res = false;
            }
            $formattedPoints = implode(",", $formattedPoints);
            $inputDistrictArea = [
                'district_id' => $district->id,
                'area' => $formattedPoints,
            ];
            /* @var $districtArea DistrictArea */
            $districtArea = DistrictArea::where(['district_id' => $district->id])->first() ? : new DistrictArea();
            $districtArea->fill($inputDistrictArea);
            if ($res){
                $districtArea->saveWithGeoSpatials();
            }
        }
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "District create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $errors)));
        }
    }
    
    public function show($id)
    {
        $model = District::find($id);
        
        return self::makeView('show', compact('model'));
    }
    
    public function destroy($id)
    {
        $model = District::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
    
    public function ajaxGetDistrictByCityId($id)
    {
        $res = Helper::createSelect(District::where(['city_id' => $id])->orderBy("name")->get(), "name");
        return Response::json($res);
    }
    
    /* Import Bulk */
    public function importCreate()
    {
        return self::makeView('import');
    }
    
    public function importDownloadTemplate()
    {
        $path = resource_path('/views/backyard/sigarang/area/district/import_data_district_template.xlsx');
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
                'Area Kecamatan (Latitude_1 Longitude_1;Latitude_2 Longitude_2;…;Latitude_n Longitude_n)',
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
                $provinceLower = strtolower($inputProvince['name']);
                $cityLower = strtolower($inputCity['name']);
                $districtLower = strtolower($inputDistrict['name']);
                $province = Province::whereRaw("LOWER(`name`) LIKE '%{$provinceLower}%'")->first();
                $city = City::whereRaw("LOWER(`name`) LIKE '%{$cityLower}%'")->first();
                $district = District::whereRaw("LOWER(`name`) LIKE '%{$districtLower}%'")->first();
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
                if ($district) {
                    $inputDistrict['city_id'] = $city->id;
                    $district->fill($inputDistrict);
                    if(!$district->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Kecamatan %s gagal', $inputDistrict['name']);
                    } else {
                        $updatedCount++;
                        $successCount++;                        
                    }
                } else {
                    $district = new District();
                    $inputDistrict['city_id'] = $city->id;
                    $district->fill($inputDistrict);
                    if(!$district->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Kecamatan %s gagal', $inputDistrict['name']);
                    } else {
                        $insertedCount++;
                        $successCount++;
                    }
                }
                if($sheet->getCellByColumnAndRow(4,$row)->getValue() != null){
                    $rawPoints = explode(";",$sheet->getCellByColumnAndRow(4, $row));
                    $formattedPoints = [];
                    foreach ($rawPoints as $point) {
                        $formattedPoints[] = $point;
                    }
                    $formattedPoints = implode(",", $formattedPoints);
                    $inputDistrictArea = [
                        'district_id' => $district->id,
                        'area' => $formattedPoints,
                    ];
                    /* @var $districtArea DistrictArea */
                    $districtArea = DistrictArea::where(['district_id' => $district->id])->first() ? : new DistrictArea();
                    $districtArea->fill($inputDistrictArea);
                    $districtArea->saveWithGeoSpatials();
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
