@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Ubah {{ ucfirst($modelName) }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
{!! Form::model($model, ['method' => 'PATCH', 'route' => [$routePrefix.'.update', $model->id]]) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection