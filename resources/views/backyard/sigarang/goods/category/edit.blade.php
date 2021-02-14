@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Edit Category
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Category</li>
@endsection

@section('content')
{!! Form::model($category, ['method' => 'PATCH', 'route' => [$routePrefix.'.update', $category->id]]) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection