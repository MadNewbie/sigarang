<?php

namespace App\Http\Controllers\Sigarang\Goods;

use App\Base\BaseController;
use App\Libraries\Mad\Helper;
use App\Models\Sigarang\Goods\Category;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use Auth;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Response;
use Yajra\DataTables\DataTables;

class GoodController extends BaseController
{
    protected static $partName = "backyard";
    protected static $moduleName = "sigarang";
    protected static $submoduleName = "goods";
    protected static $modelName = "goods";
    
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
    
    public function index()
    {
        return self::makeView('index');
    }
    
    public function indexData(Request $request)
    {
        $search = $request->get('search')['value'];
        
        $categoryTableName = Category::getTableName();
        $unitTableName = Unit::getTableName();
        $goodTableName = Goods::getTableName();
        
        $q = Goods::query()
            ->select([
                "{$categoryTableName}.name as category_name",
                "{$goodTableName}.name",
                "{$unitTableName}.name as unit_name",
                "{$goodTableName}.id",
                "{$goodTableName}.unit_id",
                "{$goodTableName}.category_id",
            ])
            ->leftJoin($categoryTableName, "{$goodTableName}.category_id", "{$categoryTableName}.id")
            ->leftJoin($unitTableName, "{$goodTableName}.unit_id", "{$unitTableName}.id");
        
            Helper::fluentMultiSearch($q, $search, [
                "{$goodTableName}.name",
                "{$categoryTableName}.name",
            ]);
            
        $res = DataTables::of($q)
            ->editColumn('name', function(Goods $v) {
                return '<a href="' . route(self::getRoutePrefix('show'),$v->id) .'">' . $v->name . '</a>';
            })
            ->editColumn('_menu', function(Goods $model) {
                return self::makeView('index-menu', compact('model'))->render();
            })
            ->rawColumns(['name', '_menu'])
            ->make(true);
        
        return $res;
    }
    
    private function _getOptions($model)
    {
        $categoryTableName = Category::getTableName();
        $unitTableName = Unit::getTableName();
        
        $categoryOptions = collect([null => 'Pilih Kategori'] + Helper::createSelect(Category::orderBy('name')->get(), 'name'));
        $unitOptions = collect([null => 'Pilih Satuan'] + Helper::createSelect(Unit::orderBy('name')->get(), 'name'));
        $options = compact([
            'model',
            'categoryOptions',
            'unitOptions',
        ]);
        return $options;
    }
    
    public function create()
    {
        /* @var $model Goods */
        $model = new Goods();
        
        $options = $this->_getOptions($model);
        return self::makeView('create', $options);
    }
    
    public function store(Request $request)
    {
        $res = true;
        $goodsTableName = Goods::getTableName();
        $this->validate($request, [
            'name' => "required|unique:{$goodsTableName},name",
            'unit_id' => "required",
            'category_id' => "required",
        ]);
        $input = $request->all();
        /* @var $model Goods */
        $model = new Goods();
        
        $model->fill($input);
        $model->created_by = Auth::user()->id;
        $model->edited_by = Auth::user()->id;
        
        $res &= $model->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Goods create successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $category->errors)));
        }
    }
    
    public function edit($id)
    {
         /* @var $model Goods */
        $model = Goods::find($id);
        
        $options = $this->_getOptions($model);
        return self::makeView('edit', $options);
    }
    
    public function update(Request $request, $id)
    {
        $res = true;
        /* @var $model Goods */
        $model = Goods::find($id);
        
        $goodsTableName = Goods::getTableName();
        $this->validate($request, [
            'name' => "required",
            'unit_id' => "required",
            'category_id' => "required",
        ]);
        $input = $request->all();
        
        $model->fill($input);
        $model->edited_by = Auth::user()->id;
        
        $res &= $model->save();
        
        if ($res) {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("success", "Goods updated successfully");
        } else {
            return redirect()->route(self::getRoutePrefix('index'))
                ->with("error", sprintf("<div>%s</div>", implode("</div><div>", $category->errors)));
        }
    }
    
    public function show($id)
    {
        /* @var $model Goods */
        $model = Goods::find($id);
        
        $options = compact(['model']);
        return self::makeView('show', $options);
    }
    
    public function destroy($id)
    {
        $model = Goods::find($id);
        return $model->delete() ? '1' : 'Data cannot be deleted';
    }
    
    public function importCreate()
    {
        return self::makeView('import');
    }
    
    public function importDownloadTemplate()
    {
        $path = resource_path('/views/backyard/sigarang/goods/goods/import_data_goods_template.xlsx');
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
                'Kategori',
                'Satuan',
                'Nama Barang',
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
                $inputCategory = [];
                $inputUnit = [];
                $inputGoods = [];
                $inputCategory['name'] = $sheet->getCellByColumnAndRow(1,$row)->getValue();
                $inputUnit['name'] = $sheet->getCellByColumnAndRow(2,$row)->getValue();
                $inputGoods['name'] = $sheet->getCellByColumnAndRow(3,$row)->getValue();
                $categoryLower = strtolower($inputCategory['name']);
                $unitLower = strtolower($inputUnit['name']);
                $goodsLower = strtolower($inputGoods['name']);
                $category = Category::whereRaw("LOWER(`name`) LIKE '%{$categoryLower}%'")->first();
                $unit = Unit::whereRaw("LOWER(`name`) LIKE '%{$unitLower}%'")->first();
                $goods = Goods::whereRaw("LOWER(`name`) LIKE '%{$goodsLower}%'")->first();
                if (!isset($category)) {
                    $category = new Category();
                    $category->fill($inputCategory);
                    if(!$category->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Kategori %s gagal', $inputCategory['name']);
                    }
                }
                if (!isset($unit)) {
                    $unit = new Unit();
                    $unit->fill($inputUnit);
                    if(!$unit->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Satuan %s gagal', $inputUnit['name']);
                    }
                }
                if ($goods) {
                    $inputGoods['category_id'] = $category->id;
                    $inputGoods['unit_id'] = $unit->id;
                    $goods->fill($inputGoods);
                    if(!$goods->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Barang %s gagal', $inputGoods['name']);
                    } else {
                        $updatedCount++;
                        $successCount++;                        
                    }
                } else {
                    $goods = new Goods();
                    $inputGoods['category_id'] = $category->id;
                    $inputGoods['unit_id'] = $unit->id;
                    $goods->fill($inputGoods);
                    if(!$goods->save()){
                        $res = false;
                        $errors[] = sprintf('Proses menyimpan Barang %s gagal', $inputGoods['name']);
                    } else {
                        $insertedCount++;
                        $successCount++;
                        
                    }
                }
            }
            $messages[] = sprintf('%s data barang berhasil diupload.', number_format($successCount,0,".",""));
            $messages[] = sprintf('%s data barang berhasil ditambahkan.', number_format($insertedCount,0,".",""));
            $messages[] = sprintf('%s data barang berhasil diperbaharui.', number_format($updatedCount,0,".",""));
            $result->message = implode('<br />', $messages);
            $res ? DB::commit() : DB::rollBack();
            
        }
        
        return Response::json($results);
    }
}

