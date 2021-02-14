@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Edit User
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">User</li>
@endsection

@section('content')
{!! Form::model($user, ['method' => 'PATCH', 'route' => [$routePrefix.'.update', $user->id, 'files' => true]]) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection