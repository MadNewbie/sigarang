<?php

namespace App\Models\Sigarang\Area;

use App\Base\BaseModel;

/**
 * @property string  $id
 * @property string  $name
 * 
 * @property City $cities
 */

class Province extends BaseModel
{
    protected $table = "sig_m_provinces";
    protected $fillable = [
        'name',
    ];
    
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}

