<?php

namespace App\Base;

use App\Http\Controllers\Controller;
use View;

class BaseController extends Controller
{
    protected static $partName = null;
    protected static $moduleName = null;
    protected static $submoduleName = null;
    protected static $modelName = null;

    public static function getModelInfo()
    {
        return [
            'partName' => static::$partName,
            'moduleName' => static::$moduleName,
            'submoduleName' => static::$submoduleName,
            'modelName' => static::$modelName,
            'routePrefix' => static::getRoutePrefix(),
            'modelPrefix' => static::getModelPrefix(),
        ];
    }

    public static function makeView($view, $data = [], $mergeData = [])
    {
        $data += static::getModelInfo();

        $pathView = [];

        $pathView[] = isset(static::$partName) ? static::$partName : "forecourt";

        $pathView[] = static::$moduleName;

        $pathView[] = !isset(static::$submoduleName) ? "": static::$submoduleName;

        $pathView[] = static::$modelName;

        $pathView[] = $view;

        $pathView = implode(".", $pathView);

        return view($pathView, $data, $mergeData);
    }

    public static function getRoutePrefix($route = null)
    {
        $routePrefix = [];

        $routePrefix[] = isset(static::$partName) ? static::$partName : "forecourt";

        $routePrefix[] = isset(static::$submoduleName) ? static::$submoduleName : static::$moduleName;

        $routePrefix[] = static::$modelName;

        if(isset($route)){
            $routePrefix[] = $route;
        }

        $routePrefix = implode(".", $routePrefix);

        return $routePrefix;
    }

    public static function getModelPrefix()
    {
        $modelPrefix = [];

        $modelPrefix[] = isset(static::$partName) ? static::$partName : "forecourt";

        $modelPrefix[] = isset(static::$submoduleName) ? static::$moduleName . "." . static::$submoduleName : static::$moduleName;

        $modelPrefix[] = static::$modelName;

        $modelPrefix = implode(".", $modelPrefix);

        return $modelPrefix;
    }
}

