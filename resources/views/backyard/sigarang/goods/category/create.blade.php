@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Create New Category
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Unit</li>
@endsection

@section('content')
{!! Form::open(array('route' => $routePrefix.'.store', 'method' => 'POST')) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection

