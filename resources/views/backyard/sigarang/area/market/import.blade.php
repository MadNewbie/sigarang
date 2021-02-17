@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Import {{ ucfirst($modelName) }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
{!! Form::open(array('route' => $routePrefix.'.import.store', 'method' => 'POST', 'files'=> true, 'class' => 'dropzone')) !!}
{!! Form::close() !!}
@endsection

@section('js-inline-data')
window['_<?=$modelName?>ImportData'] = <?= json_encode([
    'routeMarketUpload' => route('backyard.area.market.import.store'),
])?>;
@endsection

@section('js-include')
<script src='<?= asset('js/backyard/sigarang/area/market/import.js') ?>'></script>
@endsection