<?php

namespace App\Models\Sigarang\Area;

use App\Base\BaseModel;
use Auth;
use DB;

/**
 * @property string $id
 * @property string $area
 * @property string $market_id
 * 
 * @property Market $market
 */

class MarketPoint extends BaseModel
{
    protected $table = "sig_m_market_points";
    
    protected $areaFormatted = array('area');
    
    protected $fillable = [
        "area",
        "market_id",
    ];
    
    public function saveWithGeoSpatials()
    {
        $this->created_by = Auth::user()->id;
        $this->updated_by = Auth::user()->id;
        $res = true;
        DB::beginTransaction();
        if(isset($this->area)){
            $point = explode(";", $this->area);
            $this->area = DB::Raw("ST_GeomFromText('POINT({$point[0]} {$point[1]})')");
        }
        $res &= $this->save();
        $res ? DB::commit() : DB::rollback();
        return $res;
    }
    
    public function market()
    {
        return $this->belongsTo(Market::class);
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

