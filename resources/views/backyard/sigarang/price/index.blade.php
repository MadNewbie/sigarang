<?php

use App\Models\Sigarang\Price;

/* @var $model Price */
$isPrivilege = Auth::user()->can([
    $routePrefix.".approve",
    $routePrefix.".not.approve",
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
    <?= Form::hidden("selected-ids", null, ['id' => "selected-ids", "class" => "col-md-12"]) ?>
</div>
<div class="row">
    <div class="table-responsive">
        <table class="table table-hover table-striped" id="<?=$modelName?>-table" width="100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>PIC</th>
                    <th>Pasar</th>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th colspan="1"></th>
                    <th class="col-xs-1">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6"></th>
                    <?php if(Auth::user()->can([
                        $modelPrefix.".multi.action",
                    ])) : ?>
                    <th class="col-xs-1 text-center">
                        <div class="checkbox">
                            <label>
                                <input name="form-selected-id-all-checkbox" type="checkbox" title="Pilih Semua Data">
                            </label>
                        </div>
                    </th>
                    <th>
                        <button type="button"
                            title="Setujui semua yang dipilih"
                            class="btn btn-sm btn-success btn-index-menu btn-multi-action"
                            data-tag="approved"
                            >
                            <i class="far fa-check-circle"></i>
                        </button>
                        <button type="button"
                            title="Tidak Setujui semua yang dipilih"
                            class="btn btn-sm btn-danger btn-index-menu btn-multi-action"
                            data-tag="not_approved"
                            >
                            <i class="far fa-times-circle"></i>
                        </button>
                    </th>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('js-inline-data')
window['_<?=$modelName?>IndexData'] = <?= json_encode([
    'routeIndexData' => route($routePrefix.'.index.data'),
    'routeDestroyData' => route($routePrefix.'.destroy',999),
    'routeMultiAction' => route($routePrefix.'.multi.action'),
    'isPrivilege' => $isPrivilege,
    'data' => [
        'permissions' => [
            'sigarang' => [
                'multiAction' => Auth::user()->can([$routePrefix.".multi.action"]),
            ],
        ],
    ],
])?>;
@endsection

@section('js-include')
<script src="<?= asset('js/backyard/sigarang/price/index.js') ?>"></script>
@endsection

