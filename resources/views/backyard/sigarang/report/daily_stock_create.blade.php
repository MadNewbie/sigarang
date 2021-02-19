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
<?php if(Auth::user()->can("backyard.sigarang.stock.import.index")) : ?>
<div class="row">
    <div class="col-md-12">
        <div class="float-right">
            <a href="<?= route("backyard.sigarang.stock.import.index") ?>" title="Import" class="btn btn-sm btn-info">
                <i class="fa fa-upload"></i>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
{!! Form::open(array('route' => $routePrefix.'.daily.stock.store', 'method' => 'POST')) !!}
@include($modelPrefix.'._form')
{!! Form::close() !!}
@endsection

