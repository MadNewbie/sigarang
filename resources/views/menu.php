<?php

use App\Libraries\Mad\Helper;

$menus = [
    [
        'module' => 'adminlte',
        'title'  => 'Dashboard',
        'type_icon' => 'fab',
        'icon'  => 'centos',
        'route' => 'backyard.home',
    ],
    [
        'module' => 'sigarang',
        'type_icon' => 'fas',
        'title' => 'Laporan',
        'icon' => 'file-alt',
        'route' => '',
        'submodules' => [
            [
                'title' => 'Laporan Harga Harian',
                'type_icon' => 'fas',
                'icon' => 'tag',
                'route' => 'backyard.sigarang.report.daily.price.create',
                'submodule' => 'report',
            ],
            [
                'title' => 'Laporan Stok Harian',
                'type_icon' => 'fas',
                'icon' => 'clipboard-list',
                'route' => 'backyard.sigarang.report.daily.stock.create',
                'submodule' => 'report',
            ],
            [
                'title' => 'Cetak Laporan Harga',
                'type_icon' => 'fas',
                'icon' => 'print',
                'route' => 'backyard.sigarang.report.download.price.index',
                'submodule' => 'report',
            ],
            [
                'title' => 'Cetak Laporan Stok',
                'type_icon' => 'fas',
                'icon' => 'print',
                'route' => 'backyard.sigarang.report.download.stock.index',
                'submodule' => 'report',
            ],
        ],
    ],
    [
        'module' => 'goods',
        'type_icon' => 'fas',
        'title' => 'Goods',
        'icon' => 'box-open',
        'route' => '',
        'submodules' => [
            [
                'title' => 'Satuan',
                'type_icon' => 'fas',
                'icon' => 'ruler',
                'route' => 'backyard.goods.unit.index',
                'submodule' => 'unit',
            ],
            [
                'title' => 'Kategori',
                'type_icon' => 'fab',
                'icon' => 'buffer',
                'route' => 'backyard.goods.category.index',
                'submodule' => 'category',
            ],
            [
                'title' => 'Barang',
                'type_icon' => 'fas',
                'icon' => 'boxes',
                'route' => 'backyard.goods.goods.index',
                'submodule' => 'goods',
            ],
        ],
    ],
    [
        'module' => 'area',
        'type_icon' => 'fas',
        'title' => 'Area',
        'icon' => 'globe',
        'route' => '',
        'submodules' => [
            [
                'title' => 'Provinsi',
                'type_icon' => 'fas',
                'icon' => 'atlas',
                'route' => 'backyard.area.province.index',
                'submodule' => 'province',
            ],
            [
                'title' => 'Kabupaten / Kota',
                'type_icon' => 'fas',
                'icon' => 'atlas',
                'route' => 'backyard.area.city.index',
                'submodule' => 'city',
            ],
            [
                'title' => 'Kecamatan',
                'type_icon' => 'fas',
                'icon' => 'atlas',
                'route' => 'backyard.area.district.index',
                'submodule' => 'district',
            ],
            [
                'title' => 'Pasar',
                'type_icon' => 'fas',
                'icon' => 'store-alt',
                'route' => 'backyard.area.market.index',
                'submodule' => 'market',
            ],
        ],
    ],
    [
        'module' => 'price',
        'type_icon' => 'fas',
        'title' => 'Harga',
        'icon' => 'dollar-sign',
        'route' => 'backyard.sigarang.price.index',
    ],
    [
        'module' => 'stock',
        'type_icon' => 'fas',
        'title' => 'Stok',
        'icon' => 'cubes',
        'route' => 'backyard.sigarang.stock.index',
    ],
    [
        'module' => 'user',
        'title'  => 'User',
        'type_icon' => 'fas',
        'icon'  => 'user',
        'route'  => '',
        'submodules' => [
            [
                'title' => 'Role',
                'type_icon' => 'fas',
                'icon' => 'book-reader',
                'route' => 'backyard.user.role.index',
                'submodule' => 'role',
            ],
            [
                'title' => 'User',
                'type_icon' => 'fas',
                'icon' => 'id-badge',
                'route' => 'backyard.user.user.index',
                'submodule' => 'user',
            ],
        ],
    ],
];

return $menus;

