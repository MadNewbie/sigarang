@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Download {{ ucfirst($modelName) }} Stok Harian
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
{!! Form::open(array('route' => $routePrefix.'.download.stock.download', 'method' => 'POST')) !!}
@include($modelPrefix.'.download_report_form')
{!! Form::close() !!}
@endsection

@section('js-inline-data')
window['_reportFormData'] = <?= json_encode([
    'pdfLink' => route($routePrefix.'.download.stock.download.pdf'),
])?>;
@endsection

