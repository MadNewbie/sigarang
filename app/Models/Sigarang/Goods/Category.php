<?php

namespace App\Models\Sigarang\Goods;

use App\Base\BaseModel;

/**
 * @property string $id
 * @property string $name
 */

class Category extends BaseModel
{
    protected $table = "sig_m_categories";
    protected $fillable = [
        'name',
    ];
}

