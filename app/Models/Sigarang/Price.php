<?php

namespace App\Models\Sigarang;

use App\Base\BaseModel;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use Auth;
use App\Lookups\Sigarang\PriceLookup as Lookup;

/**
 * @property string $id
 * @property string $price
 * @property string $date
 * @property string $goods_id
 * @property string $market_id
 * 
 * @property Goods $goods
 * @property Market $market
 */
class Price extends BaseModel
{
    use \App\Traits\Sigarang\Price\TraitTypeStatus;
    
    protected $table = "sig_t_prices";
    protected $fillable = [
        'date',
        'price',
        'goods_id',
        'market_id',
    ];
    
    protected $attributes = [
        'type_status' => Lookup::TYPE_STATUS_NOT_APPROVED,
    ];

    public function fill(array $attributes)
    {
        parent::fill($attributes);
        $this->created_by = Auth::user()->id;
        $this->updated_by = Auth::user()->id;
    }
    
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
    
    public function market()
    {
        return $this->belongsTo(Market::class);
    }
    
    public function getFormattedDate()
    {
        return date("d F Y",strtotime($v->date));
    }
    
    public function approve()
    {
        $res = true;
        $this->type_status = Lookup::TYPE_STATUS_APPROVED;
        $this->updated_by = Auth::user()->id;
        $res &= $this->save();
        return $res;
    }
    
    public function notApprove()
    {
        $res = true;
        $this->type_status = Lookup::TYPE_STATUS_NOT_APPROVED;
        $this->updated_by = Auth::user()->id;
        $res &= $this->save();
        return $res;
    }
}

