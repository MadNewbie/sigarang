<?php

namespace App\Models\Sigarang\Area;

use App\Base\BaseModel;

/**
 * @property string  $id
 * @property string  $name
 */

class Province extends BaseModel
{
    protected $table = "sig_m_provinces";
    protected $fillable = [
        'name',
    ];
}

