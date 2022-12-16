@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Edit Role
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Role</li>
@endsection

@section('content')

{!! Form::model($role, ['method' => 'PATCH', 'route' => [$routePrefix.'.update', $role->id]]) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}

@endsection
