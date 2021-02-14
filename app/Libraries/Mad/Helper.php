<?php
namespace App\Libraries\Mad;

use Route;
use Auth;

class Helper
{
    public static function renderMenus($tmpMenus)
    {
        $menus = $tmpMenus;
        $curRoute = Route::currentRouteName();
        $tierCurRoutes = explode(".", $curRoute);
        $activeModule = '';
        $activeSubModule = '';
        ob_start();
        foreach ($menus as $menu) {
            $menu = (object) $menu;
            if (isset($menu->submodules) && count($menu->submodules) > 0) {
                foreach ($menu->submodules as $submodule) {
                    $submodule = (object) $submodule;
                    if (count($tierCurRoutes) >=3 ) {
                        if(strcmp($submodule->submodule, $tierCurRoutes[2]) == 0) {
                            $activeSubModule = $submodule->route;
                            break;
                        }
                    }else {  
                        if (array_search($submodule->submodule, $tierCurRoutes) == 2) {
                            $activeSubModule = $submodule->route;
                            break;
                        };
                    }
                }
            }
            if (array_search($menu->module, $tierCurRoutes) == 1) {
                $activeModule = $menu->module;
                break;
            };
        }
        foreach ($menus as $menu) {
            $menu = (object) $menu; ?>
            <?php if (isset($menu->submodules) && count($menu->submodules) > 0) : ?>
            <li class="nav-item <?= strcmp($activeModule, $menu->module) == 0 ? 'menu-open': ''?>">
                <a href="#" class="nav-link <?= strcmp($activeModule, $menu->module) == 0 ? 'active': ''?>">
                  <i class="nav-icon <?= $menu->type_icon ?> fa-<?= $menu->icon ?>"></i>
                  <p>
                    <?= $menu->title ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <?php foreach($menu->submodules as $submodule) :?>
                  <?php $submodule = (object) $submodule; ?>
                    <?php if (Auth::user()->can(["backyard." . $menu->module . "." . $submodule->submodule . ".index"])):?>
                    <li class="nav-item">
                      <a href="<?=route($submodule->route)?>" class="nav-link <?= strcmp($curRoute, $submodule->route) == 0 ? 'active': ''?>">
                        <i class="<?= $submodule->type_icon ?> fa-<?= $submodule->icon?> nav-icon"></i>
                        <p><?= $submodule->title?></p>
                      </a>
                    </li>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </ul>
            </li>
            <?php else : ?>
            <li class="nav-item">
                <a href="<?= route($menu->route) ?>" class="nav-link <?= strcmp($curRoute, $menu->route) == 0 ? 'active': ''?>">
                  <i class="nav-icon <?= $menu->type_icon ?> fa-<?= $menu->icon ?>"></i>
                  <p>
                    <?= $menu->title ?>
                  </p>
                </a>
            </li>
            <?php endif; ?>
        <?php };
        return ob_get_clean();
    }
    
    public static function fluentMultiSearch($rootQuery, $searchString, $fieldsCommaSeparated)
    {
        $string = explode(' ', str_replace('  ', ' ', $searchString));
        if (is_string($fieldsCommaSeparated)) {
            $fields = explode(',', $fieldsCommaSeparated);
        } else {
            $fields = $fieldsCommaSeparated;
        }
        $rootQuery->where(function() use ($rootQuery, $string, $fields) {
            foreach ($string as $v) {
                $rootQuery->where(function ($andQuery) use ($rootQuery, $fields, $v) {
                    foreach ($fields as $w) {
                        $andQuery->orWhere($w, 'LIKE', "%{$v}%");
                    }
                });
            }
        });
        return $rootQuery;
    }
    
    public static function createSelect($data, $label, $id = 'id')
    {
        $res = array();
        foreach ($data as $v) {
            $tmp = false;
            $tmp = gettype($label) === 'object' && get_class($label) === 'Closure' ? $label($v) : $v->$label;
            $tmpId = gettype($id) === 'object' && get_class($id) === 'Closure' ? $id($v) : $v->$id;
            $res[$tmpId] = $tmp;
        }
        return $res;
    }
}

