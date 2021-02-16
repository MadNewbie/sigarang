<div class="row">
    <div class="form-group col-sm-5 col-xs-12">
        {{ Form::label('market_id', 'Pasar') }}
        {{ Form::select('market_id', $marketOptions, null, ['class' => 'form-control']) }}
    </div>
</div>
<div class="row form-group">
        {{ Form::label('date', 'Tanggal', ['class' => 'col-md-12']) }} </br>
        <div class="col-md-2">
            <?= Form::text('start_date', $todayDate, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) ?>
        </div>
        <div class="col-md-1 text-center">
            s.d
        </div>
        <div class="col-md-2">
            <?= Form::text('end_date', $todayDate, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) ?>
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
<script src="<?= asset('js/backyard/sigarang/price/report.js') ?>"></script>
@endsection
