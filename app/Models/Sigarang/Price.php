<?php

namespace App\Models\Sigarang;

use App\Base\BaseModel;
use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use Auth;

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
    protected $table = "sig_t_pirces";
    protected $fillable = [
        'date',
        'price',
        'goods_id',
        'market_id',
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
}

