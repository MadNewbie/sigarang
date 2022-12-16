@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Tambah Barang
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
{!! Form::model($model ,array('route' => $routePrefix.'.store', 'method' => 'POST')) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection

