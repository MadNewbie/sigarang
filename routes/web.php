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
    Route::get('/', ['as' => 'landing_page', 'uses' => 'HomeController@index']);
});

//Route::get('/', function () {
//    return view('welcome');
//});

//Auth::routes();

Route::get('/home', 'HomeController@index', ['middleware' => ['web', 'auth', 'acl']])->name('home');

Route::get('login', 'AuthController@showLogin')->name('login');
Route::post('login', ['as' => 'login', 'uses' => 'AuthController@login'])
        ->middleware('throttle:5,1');
Route::get('logout', ['as' => 'logout', 'uses' => 'AuthController@logout']);

Route::group(['middleware' => ['web', 'auth', 'acl'], 'prefix' => 'backyard'], function() {
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
            Route::resource('districts', 'DistrictController', ['names' => 'backyard.area.district']);
            
            /*Market*/
            Route::get('markets/indexData', ['as' => 'backyard.area.market.index.data', 'uses' => 'MarketController@indexData']);
            Route::resource('markets', 'MarketController', ['names' => 'backyard.area.market']);
        });
        
        /*Prices*/
        Route::get('prices/indexData', ['as' => 'backyard.sigarang.price.index.data', 'uses' => 'PriceController@indexData']);
        Route::resource('prices', 'PriceController', ['names' => 'backyard.sigarang.price', 'only' => ['index', 'edit', 'update', 'destroy']]);
        
        /*Stocks*/
        Route::get('stocks/indexData', ['as' => 'backyard.sigarang.stock.index.data', 'uses' => 'StockController@indexData']);
        Route::resource('stocks', 'StockController', ['names' => 'backyard.sigarang.stock', 'only' => ['index', 'edit', 'update', 'destroy']]);
        
        /*Report*/
        Route::get('reports', ['as' => 'backyard.sigarang.report.index']);
        Route::get('reports/dailyPrice', ['as' => 'backyard.sigarang.report.daily.price.create', 'uses' => 'ReportController@createDailyPrice']);
        Route::post('reports/dailyPrice', ['as' => 'backyard.sigarang.report.daily.price.store', 'uses' => 'ReportController@storeDailyPrice']);
        Route::get('reports/dailyStock', ['as' => 'backyard.sigarang.report.daily.stock.create', 'uses' => 'ReportController@createDailyStock']);
        Route::post('reports/dailyStock', ['as' => 'backyard.sigarang.report.daily.stock.store', 'uses' => 'ReportController@storeDailyStock']);
    });
});

Route::group(['middleware' => ['web'], 'prefix' => 'media'], function() {
    Route::get('photo_profile', ['as' => 'media.photo_profile', 'uses' => 'MediaController@getPhotoProfile']);
});
