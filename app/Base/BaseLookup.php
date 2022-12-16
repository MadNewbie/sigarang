<?php

namespace App\Base;

/**
 * @property string $tipe
 * @property int $kode
 */

class BaseLookup
{
    protected static $_items = [];
    protected static $_labels = [];
    
    public static function getItems()
    {
        return [];
    }
    
    public static function loadItems($tipe)
    {
        if (!isset(self::$_items[$tipe])) {
            self::$_items = static::getItems();
        }
        
        return self::$_items[$tipe];
    }
    
    public static function items($tipe, $except = [])
    {
        $tmp = self::loadItems($tipe);
        foreach ($except as $id) {
            unset($tmp[$id]);
        }
        return $tmp;
    }

    public static function item($tipe, $kode)
    {
        self::loadItems($tipe);
        return isset(self::$_items[$tipe][$kode]) ? self::$_items[$tipe][$kode] : '';
    }
}

