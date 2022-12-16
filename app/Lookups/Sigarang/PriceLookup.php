<?php

namespace App\Lookups\Sigarang;

use App\Base\BaseLookup;

class PriceLookup extends BaseLookup
{
    /*
     * Constants - Type
     */
    
    const TYPE_STATUS = 'type_status';
    
    /*
     * Constants - Value
     */
    
    const TYPE_STATUS_NOT_APPROVED = 50;
    const TYPE_STATUS_APPROVED = 100;
    
    /*
     * Item List
     */
    public static function getItems()
    {
        return [
            self::TYPE_STATUS => [
                self::TYPE_STATUS_APPROVED => 'Disetujui',
                self::TYPE_STATUS_NOT_APPROVED => 'Belum Disetujui',
            ],
        ];
    }
}

