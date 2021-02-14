<?php

use App\User;

/* @var $model User */
$isPrivilege = Auth::user()->can([
    $routePrefix.".create",
    $routePrefix.".destroy",
    $routePrefix.".edit",
]);
?>
@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    {{ ucfirst($modelName) }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
<div class="row">
    <div class="table-responsive">
        <table class="table table-hover table-striped" id="<?=$modelName?>-table" width="100%">
            <thead>
                <tr>
                    <th>Nama</th>
                    <?php if ($isPrivilege) : ?>
                        <th class="col-xs-1">
                            <?php if(Auth::user()->can($routePrefix.".create")) : ?>
                            <a href="<?= route($routePrefix.".create") ?>" title="Create" class="btn btn-sm btn-success">
                                <i class="fa fa-plus"></i>
                            </a>
                            <?php endif; ?>
                        </th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js-inline-data')
window['_<?=$modelName?>IndexData'] = <?= json_encode([
    'routeIndexData' => route($routePrefix.'.index.data'),
    'routeDestroyData' => route($routePrefix.'.destroy',999),
    'isPrivilege' => $isPrivilege,
])?>;
@endsection

@section('js-include')
<script src="<?= asset('js/backyard/sigarang/area/market/index.js') ?>"></script>
@endsection

