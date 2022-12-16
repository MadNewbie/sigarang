<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        {!! Form::label('market_id', 'Pasar') !!}
        <div class="form-control">{{ $model->market->name }}</div>
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        {!! Form::label('goods_id', 'Barang') !!}
        <div class="form-control">{{ $model->goods->name }}</div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-3">
        {!! Form::label('date', 'Tanggal') !!}
        {!! Form::text('date', null, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) !!}
    </div>
    <div class="form-group col-xs-12 col-sm-3">
        {!! Form::label('stock', 'Stok') !!}
        {!! Form::text('stock', null, ['class' => 'form-control text-right']) !!}
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-6 col-sm-2">
        <a class="btn btn-primary" href="{{ URL::previous() }}" style="width:100%">Back</a>
    </div>
    <div class="form-group col-xs-6 col-sm-2">
        <button type="submit" class="btn btn-success" style="width:100%">Submit</button>
    </div>
</div>

@section('js-inline-data')
window['_<?=$modelName?>FormData'] = <?= json_encode([
])?>;
@endsection

@section('js-include')
<script src="<?= asset('js/backyard/sigarang/stock/form.js') ?>"></script>
@endsection
