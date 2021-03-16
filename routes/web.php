<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['web']], function() {
    Route::get('/', ['as' => 'landing_page', 'uses' => 'HomeController@landingPage']);
    Route::post('/getMapData', 'HomeController@getMapData')->name('forecourt.get.map.data');
    Route::post('/getGraphData', 'HomeController@getGraphData')->name('forecourt.get.graph.data');
});

//Route::get('/', function () {
//    return view('welcome');
//});

//Auth::routes();


Route::get('login', 'AuthController@showLogin')->name('login');
Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login'])
        ->middleware('throttle:5,1');
Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

Route::group(['middleware' => ['web', 'auth', 'acl'], 'prefix' => 'backyard'], function() {
    Route::get('/home', 'BackyardController@index')->name('backyard.home');
    Route::post('/getPriceGraphData', 'BackyardController@getPriceGraphData')->name('backyard.get.price.graph.data');
    Route::post('/getStockGraphData', 'BackyardController@getStockGraphData')->name('backyard.get.stock.graph.data');
    Route::post('/getMapData', 'BackyardController@getMapData')->name('backyard.get.map.data');
    
    Route::group(['namespace' => 'User', 'prefix' => 'user'], function() {
        
        /*Roles*/
        Route::get('roles/indexData', ['as' => 'backyard.user.role.index.data', 'uses' => 'RoleController@indexData']);
        Route::resource('roles', 'RoleController', ['names' => 'backyard.user.role']);
        
        /*Users*/
        Route::get('users/indexData', ['as' => 'backyard.user.user.index.data', 'uses' => 'UserController@indexData']);
        Route::resource('users', 'UserController', ['names' => 'backyard.user.user']);
    });
    
    Route::group(['namespace' => 'Sigarang', 'prefix' => 'sigarang'], function() {
        Route::group(['namespace' => 'Goods', 'prefix' => 'goods'], function() {
            
            /*Units*/
            Route::get('units/indexData', ['as' => 'backyard.goods.unit.index.data', 'uses' => 'UnitController@indexData']);
            Route::resource('units', 'UnitController', ['names' => 'backyard.goods.unit']);
            
            /*Categories*/
            Route::get('categories/indexData', ['as' => 'backyard.goods.category.index.data', 'uses' => 'CategoryController@indexData']);
            Route::resource('categories', 'CategoryController', ['names' => 'backyard.goods.category']);
            
            /*Goods*/
            Route::get('goods/indexData', ['as' => 'backyard.goods.goods.index.data', 'uses' => 'GoodController@indexData']);
            Route::get('goods/import', ['as' => 'backyard.goods.goods.import.index', 'uses' => 'GoodController@importCreate']);
            Route::post('goods/import', ['as' => 'backyard.goods.goods.import.store', 'uses' => 'GoodController@importStore']);
            Route::get('goods/import/downloadTemplate', ['as' => 'backyard.goods.goods.import.download.template', 'uses' => 'GoodController@importDownloadTemplate']);
            Route::resource('goods', 'GoodController', ['names' => 'backyard.goods.goods']);
        });
        
        Route::group(['namespace' => 'Area', 'prefix' => 'area'], function() {
            
            /*Provinces*/
            Route::get('provinces/indexData', ['as' => 'backyard.area.province.index.data', 'uses' => 'ProvinceController@indexData']);
            Route::resource('provinces', 'ProvinceController', ['names' => 'backyard.area.province']);
            
            /*Cities*/
            Route::get('cities/ajaxGetCityByProvinceId/{id}', ['as' => 'backyard.area.city.ajax.get.city.by.province.id', 'uses' => 'CityController@ajaxGetCityByProvinceId']);
            Route::get('cities/indexData', ['as' => 'backyard.area.city.index.data', 'uses' => 'CityController@indexData']);
            Route::resource('cities', 'CityController', ['names' => 'backyard.area.city']);
            
            /*Districts*/
            Route::get('districts/ajaxGetDistrictByCityId/{id}', ['as' => 'backyard.area.district.ajax.get.district.by.city.id', 'uses' => 'DistrictController@ajaxGetDistrictByCityId']);
            Route::get('districts/indexData', ['as' => 'backyard.area.district.index.data', 'uses' => 'DistrictController@indexData']);
            Route::get('districts/import', ['as' => 'backyard.area.district.import.index', 'uses' => 'DistrictController@importCreate']);
            Route::post('districts/import', ['as' => 'backyard.area.district.import.store', 'uses' => 'DistrictController@importStore']);
            Route::get('districts/import/downloadTemplate', ['as' => 'backyard.area.district.import.download.template', 'uses' => 'DistrictController@importDownloadTemplate']);
            Route::resource('districts', 'DistrictController', ['names' => 'backyard.area.district']);
            
            /*Market*/
            Route::get('markets/indexData', ['as' => 'backyard.area.market.index.data', 'uses' => 'MarketController@indexData']);
            Route::get('markets/import', ['as' => 'backyard.area.market.import.index', 'uses' => 'MarketController@importCreate']);
            Route::post('markets/import', ['as' => 'backyard.area.market.import.store', 'uses' => 'MarketController@importStore']);
            Route::get('markets/import/downloadTemplate', ['as' => 'backyard.area.market.import.download.template', 'uses' => 'MarketController@importDownloadTemplate']);
            Route::resource('markets', 'MarketController', ['names' => 'backyard.area.market']);
        });
        
        /*Prices*/
        Route::get('prices/indexData', ['as' => 'backyard.sigarang.price.index.data', 'uses' => 'PriceController@indexData']);
        Route::get('prices/import', ['as' => 'backyard.sigarang.price.import.index', 'uses' => 'PriceController@importCreate']);
        Route::post('prices/import', ['as' => 'backyard.sigarang.price.import.store', 'uses' => 'PriceController@importStore']);
        Route::get('prices/import/downloadTemplate', ['as' => 'backyard.sigarang.price.import.download.template', 'uses' => 'PriceController@importDownloadTemplate']);
        Route::get('prices/{id}/approve', ['as' => 'backyard.sigarang.price.approve', 'uses' => 'PriceController@approvingPrice']);
        Route::get('prices/{id}/notApprove', ['as' => 'backyard.sigarang.price.not.approve', 'uses' => 'PriceController@notApprovingPrice']);
        Route::post('prices/multiAction', ['as' => 'backyard.sigarang.price.multi.action', 'uses' => 'PriceController@multiAction']);
        Route::resource('prices', 'PriceController', ['names' => 'backyard.sigarang.price', 'only' => ['index', 'edit', 'update', 'destroy']]);
        
        /*Stocks*/
        Route::get('stocks/indexData', ['as' => 'backyard.sigarang.stock.index.data', 'uses' => 'StockController@indexData']);
        Route::get('stocks/import', ['as' => 'backyard.sigarang.stock.import.index', 'uses' => 'StockController@importCreate']);
        Route::post('stocks/import', ['as' => 'backyard.sigarang.stock.import.store', 'uses' => 'StockController@importStore']);
        Route::get('stocks/import/downloadTemplate', ['as' => 'backyard.sigarang.stock.import.download.template', 'uses' => 'StockController@importDownloadTemplate']);
        Route::get('stocks/{id}/approve', ['as' => 'backyard.sigarang.stock.approve', 'uses' => 'StockController@approvingStock']);
        Route::get('stocks/{id}/notApprove', ['as' => 'backyard.sigarang.stock.not.approve', 'uses' => 'StockController@notApprovingStock']);
        Route::post('stocks/multiAction', ['as' => 'backyard.sigarang.stock.multi.action', 'uses' => 'StockController@multiAction']);
        Route::resource('stocks', 'StockController', ['names' => 'backyard.sigarang.stock', 'only' => ['index', 'edit', 'update', 'destroy']]);
        
        /*Report*/
        Route::get('reports', ['as' => 'backyard.sigarang.report.index']);
        Route::get('reports/dailyPrice', ['as' => 'backyard.sigarang.report.daily.price.create', 'uses' => 'ReportController@createDailyPrice']);
        Route::post('reports/dailyPrice', ['as' => 'backyard.sigarang.report.daily.price.store', 'uses' => 'ReportController@storeDailyPrice']);
        Route::get('reports/dailyStock', ['as' => 'backyard.sigarang.report.daily.stock.create', 'uses' => 'ReportController@createDailyStock']);
        Route::post('reports/dailyStock', ['as' => 'backyard.sigarang.report.daily.stock.store', 'uses' => 'ReportController@storeDailyStock']);
        Route::get('reports/downloadPriceIndex', ['as' => 'backyard.sigarang.report.download.price.index', 'uses' => 'ReportController@reportPriceIndex']);
        Route::post('reports/downloadPricePost', ['as' => 'backyard.sigarang.report.download.price.download', 'uses' => 'ReportController@reportPricePost']);
        Route::get('reports/downloadStockIndex', ['as' => 'backyard.sigarang.report.download.stock.index', 'uses' => 'ReportController@reportStockIndex']);
        Route::post('reports/downloadStockPost', ['as' => 'backyard.sigarang.report.download.stock.download', 'uses' => 'ReportController@reportStockPost']);
    });
});

Route::group(['middleware' => ['web'], 'prefix' => 'media'], function() {
    Route::get('photo_profile', ['as' => 'media.photo_profile', 'uses' => 'MediaController@getPhotoProfile']);
});
