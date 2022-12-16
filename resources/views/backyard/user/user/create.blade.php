@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Create New User
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">User</li>
@endsection

@section('content')
{!! Form::open(array('route' => $routePrefix.'.store', 'method' => 'POST', 'files' => true)) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection

