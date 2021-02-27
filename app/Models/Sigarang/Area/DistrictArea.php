<?php

namespace App\Models\Sigarang\Area;

use App\Base\BaseModel;
use Auth;
use DB;

/**
 * @property string $id
 * @property string $area
 * @property string $district_id
 * 
 * @property District $district
 */

class DistrictArea extends BaseModel
{
    protected $table = "sig_m_district_areas";
    
    protected $areaFormatted = array('area');
    
    protected $fillable = [
        "area",
        "district_id",
    ];
    
    public function saveWithGeoSpatials()
    {
        $this->created_by = Auth::user()->id;
        $this->updated_by = Auth::user()->id;
        $res = true;
        DB::beginTransaction();
        if(isset($this->area)){
            $points = explode(";", $this->area);
            $areaRaw = implode(",", $points);
            $this->area = DB::Raw("ST_GeomFromText('POLYGON(({$areaRaw}))')");
        }
        $res &= $this->save();
        $res ? DB::commit() : DB::rollback();
        return $res;
    }
    
    public function district()
    {
        return $this->belongsTo(District::class);
    }
    
    public function getPoint()
    {
        return DB::select(DB::Raw("Select ST_AsGeoJSON(ST_GeomFromText('{$this->area}')) as point")->getValue(),[1])[0]->point;
    }
    
    function newQuery($excludeDeleted = true) {
        $raw='';
        foreach($this->areaFormatted as $column){
            $raw .= ' astext('.$column.') as '.$column.' ';
        }
        return parent::newQuery($excludeDeleted)->addSelect('*',\DB::raw($raw));
    }
}

