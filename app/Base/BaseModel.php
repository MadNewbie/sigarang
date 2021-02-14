<?php

namespace App\Base;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{    
    static public function getTableName() {
        return with(new static)->getTable();
    } 
}

