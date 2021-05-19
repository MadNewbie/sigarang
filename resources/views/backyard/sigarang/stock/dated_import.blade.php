@extends('backyard.layout')

@section('pagetitle')
    | {{ ucfirst($modelName) }}
@endsection

@section('submodule-header')
    Dated Import Data {{ ucfirst($modelName) }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">{{ ucfirst($modelName) }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="float-right">
            <a href="<?= route("{$routePrefix}.dated.import.download.template") ?>" class="btn btn-warning btn-sm">
                <i class="fa fa-cloud-download"></i>
                Download Template
            </a>
        </div>
    </div>
</div>
{!! Form::open(array('route' => $routePrefix.'.dated.import.store', 'method' => 'POST', 'files'=> true)) !!}
<div class="row form-group">
    <div class="col-md-12 form-control text-center">
        <input type="file" id="file-upload" />
    </div>
</div>
{!! Form::close() !!}
@endsection

@section('js-inline-data')
window['_<?=$modelName?>ImportData'] = <?= json_encode([
    'routeStockUpload' => route('backyard.sigarang.stock.dated.import.store'),
])?>;
@endsection

@section('js-include')
<script src='<?= asset('js/backyard/sigarang/stock/import.js') ?>'></script>
@endsection
