@extends('forecourt.layout')

@section('css-include-before')
<link  rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
   integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
   crossorigin="">
@endsection

@section('css-inline')
.masthead {
    padding-top: 0rem;
    padding-bottom: 0rem;
    overflow: visible;
}

#masthead-strip {
    top: 2rem;
    padding: 1rem;
    position: absolute;
    z-index: 500;
    background-color: #808080;
}

#sidebar-wrapper {
    z-index: 1100;
    background-image: linear-gradient( #808080, #343a40);
}

.content-section {
    width: 100%;
    background: black;
    overflow: hidden;
    padding-top: 2rem;
    padding-bottom: 2rem;
    background: -webkit-linear-gradient(#808080, #343a40), url("https://www.akseleran.co.id/blog/wp-content/uploads/2020/10/Pasar-Persaingan-Sempurna.jpg");
    background: linear-gradient(rgba(128,128,128,0.8), rgba(52,58,64,0.8)), url("https://www.akseleran.co.id/blog/wp-content/uploads/2020/10/Pasar-Persaingan-Sempurna.jpg");
}

img {
   object-fit: cover;
}

footer.footer {
    padding-top: 1rem;
    padding-bottom: 1rem;
    background-color: rgba(52,58,64,0.8);
}

#map-info-box{
    position:absolute;
    display:none;
    transform:translate(-50%,-50%);
    height:130px;
    width:300px;
    background:#fff;
    border-radius:5%;
    border:2px solid black;
}

#map-info-legend{
    display: none;
    background:#fff;
    border-radius:5%;
    border:2px solid black;
}

#map-info-avg-price{
    display: none;
    background:#fff;
    border-radius:5%;
    border:2px solid black;
}

.carousel-item{
    margin-left:12%;
    margin-right:12%;
    width:76%;
}
.carousel-item>.row{
    margin-bottom:2.5%;
    margin-top:2.5%;
}
@endsection

@section('content')
<div id="map-info-box">
    <div class="container row">
        <div class="col-md-12" id="map-info-box-title" style="font-weight: bold">
        </div>
        <div class="col-md-12" id="map-info-box-price">
        </div>
        <div class="col-md-12" id="map-info-box-stock" style="font-size: 0.75rem">
        </div>
        <div class="col-md-12" id="map-info-box-note" style="font-size: 0.75rem">
        </div>
    </div>
</div>
<div id="map-info-avg-price">
    <div class="container">
        <span>Harga rata-rata kabupaten / kota</span>
        <h2 id="map-avg-value"></h2>
    </div>
</div>
<div id="map-info-legend">
    <div class="container">
        <dl class="dl-vertical">
            <dt>Legenda</dt>
            <dt id="map-info-box-avg-title" style="font-weight: bold">
                <div style="background: #ff4636; height: 10px; width: 10px">
                </div>
            </dt>
            <dd id="map-info-box-avg-value">
                Harga barang > 10% dari harga rata-rata
            </dd>
            <dt style="font-weight: bold">
                <div style="background: #fbe452; height: 10px; width: 10px">
                </div>
            </dt>
            <dd>
                Harga barang < 10% dari harga rata-rata
            </dd>
            <dt style="font-weight: bold">
                <div style="background: #3f972d; height: 10px; width: 10px">
                </div>
            </dt>
            <dd>
                Harga barang berada pada rentang 10% harga rata-rata
            </dd>
            <dt style="font-weight: bold">
                <div style="background: #808080; height: 10px; width: 10px">
                </div>
            </dt>
            <dd>
                Wilayah yang belum memasukkan data harga barang
            </dd>
        </dl>
    </div>
</div>
<!-- Map -->
<header class="masthead">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6" id="masthead-strip">
                <div class="row">
                    <div class="col-md-6 text-truncate">
                        <a class="navbar-brand text-white" href="#">
                            <img src="{{asset('appicon.png')}}" height="30rem"/>
                            <span>Sistem Informasi Harga Pasar</span>
                        </a>
                    </div>
                    <div class="col-md-3 form-group">
                        {{Form::text('map-date', null, ['class' => 'form-control datepicker', 'id' => 'map-date'])}}
                    </div>
                    <div class="col-md-3 form-group">
                        {{Form::select('goods_id', $goodsSelect, null, ['class' => 'form-control', 'id'=>'map-goods'])}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="map-section" style="background-color: black; width: 100%; height: 100vh;">
    </div>
</header>

<!-- Price Graph -->
<section class="content-section text-white" id="perubahan-harga">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h3 class="mb-5">Informasi Perubahan Harga</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row justify-content-md-center">
                    <div class="form-group col-md-3">
                        {{Form::text('graph-date', null, ['class' => 'datepicker form-control col-md-12 col-sm-12', 'id' => 'graph-date'])}}
                    </div>
                    <div class="form-group col-md-3">
                        {{Form::select('market_id', $marketSelect, null, ['class' => 'form-control col-md-12 col-sm-12', 'id'=>'graph-market'])}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 mx-auto" style="background: rgba(92,97,102,0.8);">
                <div class="carousel slide" data-ride="carousel" id="priceCarousel">
                    <div class="carousel-inner">
                        <div class="carousel-item">
                            <div class="row">
                                <div class="info-box col-md-4">
                                    <div class="row info-box-title">
                                        <div class="col-md-12">
                                            Title
                                        </div>
                                    </div>
                                    <div class="row info-box-content">
                                        <canvas class="col-md-6 content-graph">

                                        </canvas>
                                        <div class="col-md-6 content-info">
                                            Info
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#priceCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#priceCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Stock Graph -->
<section class="content-section text-white" id="perubahan-stok">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h3 class="mb-5">Informasi Perubahan Stok</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="row justify-content-md-center">
                    <div class="form-group col-md-3">
                        {{Form::text('stock-date', null, ['class' => 'datepicker form-control col-md-12 col-sm-12', 'id' => 'stock-date'])}}
                    </div>
                    <div class="form-group col-md-3">
                        {{Form::select('stock_id', $marketSelect, null, ['class' => 'form-control col-md-12 col-sm-12', 'id'=>'stock-market'])}}
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 mx-auto" style="background: rgba(92,97,102,0.8)">
                <div class="carousel slide" data-ride="carousel" id="stockCarousel">
                    <div class="carousel-inner">
                        <div class="carousel-item">
                            <div class="row">
                                <div class="info-box col-md-4">
                                    <div class="row info-box-title">
                                        <div class="col-md-12">
                                            Title
                                        </div>
                                    </div>
                                    <div class="row info-box-content">
                                        <canvas class="col-md-6 content-graph">

                                        </canvas>
                                        <div class="col-md-6 content-info">
                                            Info
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a class="carousel-control-prev" href="#stockCarousel" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#stockCarousel" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js-inline-data')
window['_landingPageData'] = <?= json_encode([
    'routeGetMapData' => route('forecourt.get.map.data'),
    'routeGetGraphData' => route('forecourt.get.graph.data'),
    'routeGetStockGraphData' => route('forecourt.get.stock.graph.data'),
])?>;
@endsection

@section('js-include')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
   integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
   crossorigin=""></script>
<script src="<?= asset('js/forecourt/leaflet_landing_page.js') ?>"></script>
@endsection
