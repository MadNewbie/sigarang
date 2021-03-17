<?php

use App\Models\Sigarang\Price;

/* @var $model Price */
$isPrivilege = Auth::user()->can([
    $routePrefix.".approve",
    $routePrefix.".not.approve",
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
                    <th>Stok Tersedia</th>
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

<?php ob_start(); ?>
<label class="form-group col-md-3">
    Pasar
    <?= Form::select('market', $marketList, null, ['class' => 'form-control form-control-sm']) ?>
</label>
<label class="form-group col-md-3">
    Barang
    <?= Form::select('goods', $goodsList, null, ['class' => 'form-control form-control-sm']) ?>
</label>
<label class="form-group col-md-3">
    Status
    <?= Form::select('type_status', [null => 'Semua'] + App\Lookups\Sigarang\PriceLookup::items(App\Lookups\Sigarang\PriceLookup::TYPE_STATUS), null, ['class' => 'form-control form-control-sm']) ?>
</label>
<?php $marketList = preg_replace('/[ ]+/', ' ', preg_replace('/[\r\n]/', '', ob_get_clean())); ?>
<?php $goodsList = preg_replace('/[ ]+/', ' ', preg_replace('/[\r\n]/', '', ob_get_clean())); ?>
<?php $statusList = preg_replace('/[ ]+/', ' ', preg_replace('/[\r\n]/', '', ob_get_clean())); ?>

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
        'template' => [
            'marketList' => $marketList,
            'goodsList' => $goodsList,
            'statusList' => $statusList,
        ],
    ],
])?>;
@endsection

@section('js-include')
<script src="<?= asset('js/backyard/sigarang/stock/index.js') ?>"></script>
@endsection

