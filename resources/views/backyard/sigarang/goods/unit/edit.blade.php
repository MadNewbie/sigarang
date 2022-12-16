@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Edit Unit
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Unit</li>
@endsection

@section('content')
{!! Form::model($unit, ['method' => 'PATCH', 'route' => [$routePrefix.'.update', $unit->id]]) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection