@extends('backyard.layout')

@section('css-include-before')
<link  rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin="">
@endsection

@section('css-inline')
#map-info-box{
    position:absolute;
    display:none;
    transform:translate(-50%,-50%);
    height:100px;
    width:250px;
    background:#fff;
    border-radius:5%;
    border:2px solid black;
}

#map-info-legend{
    display: none;
    height:10%;
    background:#fff;
    border-radius:5%;
    border:2px solid black;
}
@endsection

@section('submodule-header')
    Dashboard
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Home</li>
@endsection

@section('content')
<div id="map-info-box">
    <div class="container row">
        <div class="container row">
            <div class="col-md-12" id="map-info-box-title" style="font-weight: bold">
            </div>
            <div class="col-md-12" id="map-info-box-note" style="font-size: 0.75rem">
            </div>
        </div>
    </div>
</div>
<div id="map-info-legend">
    <div class="container" style="background:#f8f9fa;">
        <span><div style="background: #ff4636; height: 10px; width: 10px; margin-right: 2px; display: inline-block"></div>Data terkumpul < 30% </span>
        <span><div style="background: #fbe452; height: 10px; width: 10px; margin-right: 2px; display: inline-block"></div>30% <= Data terkumpul < 50% dan >= 30% </span>
        <span><div style="background: #3f972d; height: 10px; width: 10px; margin-right: 2px; display: inline-block"></div>Data terkumpul >= 50% </span>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Kelengkapan Data</h3>
                <div class="card-tools">
                    {{Form::text('date', null, ["class" => "form-group datepicker", "id" => "map-date"])}}
                </div>
            </div>
            <div class="card-body" style="display: block; height: 70vh">
               <div id="map-section" style="height:100%; width: 100%;">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Dinamika Harga</h3>
                <div class="card-tools">
                    {{Form::select('market_id', $marketSelect, null, ["class" => "form-group", "id" => "price-market-select"])}}
                    {{Form::select('goods_id', $goodsSelect, null, ["class" => "form-group", "id" => "price-goods-select"])}}
                </div>
            </div>
            <div class="card-body" style="display: block; height: 45vh">
                <canvas id='price-graph'>
                </canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Dinamika Stok</h3>
                <div class="card-tools">
                    {{Form::select('market_id', $marketSelect, null, ["class" => "form-group", "id" => "stock-market-select"])}}
                    {{Form::select('goods_id', $goodsSelect, null, ["class" => "form-group", "id" => "stock-goods-select"])}}
                </div>
            </div>
            <div class="card-body" style="display: block; height: 45vh">
                <canvas id='stock-graph'>
                </canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-inline-data')
window['_dashboardData'] = <?= json_encode([
    'routeGetMapData' => route('backyard.get.map.data'),
    'routeGetPriceGraphData' => route('backyard.get.price.graph.data'),
    'routeGetStockGraphData' => route('backyard.get.stock.graph.data'),
])?>;
@endsection

@section('js-include')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
   integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
   crossorigin=""></script>
<script src="<?= asset('js/backyard/leaflet_dashboard.js') ?>"></script>
@endsection

