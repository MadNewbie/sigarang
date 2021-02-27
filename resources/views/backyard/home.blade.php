@extends('backyard.layout')

@section('submodule-header')
    Dashboard
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Home</li>
@endsection

@section('content')
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
<script src="<?= asset('js/backyard/dashboard.js') ?>"></script>
@endsection

