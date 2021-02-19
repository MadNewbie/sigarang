const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/js/app.js', 'public/js')

    /* Module User */
    /* Role */
    .js('resources/js/backyard/user/role/index.js', 'public/js/backyard/user/role')
    /* User */
    .js('resources/js/backyard/user/user/index.js', 'public/js/backyard/user/user')

    /* Module Sigarang */
    /* Goods */
    /* Unit */
    .js('resources/js/backyard/sigarang/goods/unit/index.js', 'public/js/backyard/sigarang/goods/unit')
    /* Unit */
    .js('resources/js/backyard/sigarang/goods/category/index.js', 'public/js/backyard/sigarang/goods/category')
    /* Goods */
    .js('resources/js/backyard/sigarang/goods/goods/index.js', 'public/js/backyard/sigarang/goods/goods')
    .js('resources/js/backyard/sigarang/goods/goods/import.js', 'public/js/backyard/sigarang/goods/goods')

    /* Area */
    /* Province */
    .js('resources/js/backyard/sigarang/area/province/index.js', 'public/js/backyard/sigarang/area/province')
    /* City */
    .js('resources/js/backyard/sigarang/area/city/index.js', 'public/js/backyard/sigarang/area/city')
    /* District */
    .js('resources/js/backyard/sigarang/area/district/index.js', 'public/js/backyard/sigarang/area/district')
    .js('resources/js/backyard/sigarang/area/district/form.js', 'public/js/backyard/sigarang/area/district')
    /* Market */
    .js('resources/js/backyard/sigarang/area/market/index.js', 'public/js/backyard/sigarang/area/market')
    .js('resources/js/backyard/sigarang/area/market/form.js', 'public/js/backyard/sigarang/area/market')
    .js('resources/js/backyard/sigarang/area/market/import.js', 'public/js/backyard/sigarang/area/market')

    /* Price */
    .js('resources/js/backyard/sigarang/price/index.js', 'public/js/backyard/sigarang/price')
    .js('resources/js/backyard/sigarang/price/form.js', 'public/js/backyard/sigarang/price')
    .js('resources/js/backyard/sigarang/price/report.js', 'public/js/backyard/sigarang/price')
    /* Stock */
    .js('resources/js/backyard/sigarang/stock/index.js', 'public/js/backyard/sigarang/stock')
    .js('resources/js/backyard/sigarang/stock/form.js', 'public/js/backyard/sigarang/stock')
    .js('resources/js/backyard/sigarang/stock/report.js', 'public/js/backyard/sigarang/stock')
    /* Report */
    .js('resources/js/backyard/sigarang/report/_form.js', 'public/js/backyard/sigarang/report')
    
    .sass('resources/sass/app.scss', 'public/css');
