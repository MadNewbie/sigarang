<?php

namespace App\Models\Sigarang\Goods;

use App\Base\BaseModel;

/**
 * @property string $id
 * @property string $name
 * 
 * @property Goods $goods
 */

class Category extends BaseModel
{
    protected $table = "sig_m_categories";
    protected $fillable = [
        'name',
    ];
    
    public function goods()
    {
        return $this->hasMany(Goods::class);
    }
}

