@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    {{ ucfirst($modelName) }} Stok Harian
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
{!! Form::open(array('route' => $routePrefix.'.daily.stock.store', 'method' => 'POST')) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection

