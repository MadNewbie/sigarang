@extends('forecourt.layout')

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
    z-index: 10;
    background-color: rgba(31,110,70,1);
}

#sidebar-wrapper {
    z-index: 15;
    background-image: linear-gradient( rgba(31,110,70,1), #343a40);
}

.content-section {
    width: 100%;
    height: 95%;
    background: black;
    overflow: hidden;
    padding-top: 2rem;
    padding-bottom: 2rem;
    background: -webkit-linear-gradient(rgba(31,110,70,0.8), rgba(52,58,64,0.8)), url("https://www.akseleran.co.id/blog/wp-content/uploads/2020/10/Pasar-Persaingan-Sempurna.jpg");
    background: linear-gradient(rgba(31,110,70,0.8), rgba(52,58,64,0.8)), url("https://www.akseleran.co.id/blog/wp-content/uploads/2020/10/Pasar-Persaingan-Sempurna.jpg");
}

img {
   object-fit: cover;
}

footer.footer {
    padding-top: 1rem;
    padding-bottom: 1rem;
    background-color: rgba(52,58,64,0.8);
}
@endsection

@section('content')
<!-- Header -->
<header class="masthead">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6" id="masthead-strip">
                <a class="navbar-brand text-white" href="#">
                    <img src="https://2.bp.blogspot.com/-Ne5sknY1pJw/WhUK2mTUbUI/AAAAAAAAFPY/PnobQKmeO3Ev71-6TSlFunw08Pnk3LpogCLcBGAs/s1600/Sampang.png" height="30rem"/> Sistem Informasi Harga Barang
                </a>
                {{Form::select('goods_id', [null=>"Pilih Barang"], null, ['class' => 'float-right form-control col-md-3 col-sm-6', 'id'=>'map-goods'])}}
                {{Form::text('map-date', null, ['class' => 'float-right datepicker form-control col-md-3 col-sm-6', 'id' => 'map-date'])}}
            </div>
        </div>
    </div>
    <div id="map-section" style="background-color: black; width: 100%; height: 100vh;">
    </div>
</header>

<!-- About -->
<section class="content-section text-white" id="perubahan">
    <div class="container text-center">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <h3 class="mb-5">Informasi Perubahan Harga</h3>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js-inline-data')
window['_landingPageData'] = <?= json_encode([
//    'routeGetMapData' => route('forecourt.get.map.data'),
//    'routeGetPriceData' => route('forecourt.get.price.data'),
])?>;
@endsection

@section('js-include')
<script src="<?= asset('js/forecourt/landing_page.js') ?>"></script>
@endsection