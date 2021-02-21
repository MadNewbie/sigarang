<?php

namespace App\Traits\Sigarang\Stock;

use App\Lookups\Sigarang\StockLookup as Lookup;

/**
 * @property integer $type_status
 */

trait TraitTypeStatus
{
    public function getTypeStatus()
    {
        return Lookup::item(Lookup::TYPE_STATUS, $this->type_status);
    }
    
    public static function getTypeStatusList()
    {
        return Lookup::items(Lookup::TYPE_STATUS);
    }
    
    public function isTypeStatusApproved()
    {
        return $this->type_status == Lookup::TYPE_STATUS_APPROVED;
    }
    
    public function isTypeStatusNotApproved()
    {
        return $this->type_status == Lookup::TYPE_STATUS_NOT_APPROVED;
    }
    
    public function getTypeStatusBadge()
    {
        $class = '';
        switch ($this->type_status) {
            case Lookup::TYPE_STATUS_APPROVED:
                $class = 'success';
                break;
            case Lookup::TYPE_STATUS_NOT_APPROVED:
                $class = 'secondary';
                break;
        }
        return sprintf(
            '<span class="badge badge-%s">%s</span>',
            $class,
            $this->getTypeStatus()
        );
    }
}

