<div class="row">
    <div class="form-group col-sm-5 col-xs-12">
        {{ Form::label('market_id', 'Pasar') }}
        {{ Form::select('market_id', $marketOptions, null, ['class' => 'form-control']) }}
    </div>
    <div class="form-group col-sm-5 col-xs-12">
        <div class="row form-group">
                {{ Form::label('date', 'Tanggal', ['class' => 'col-md-12']) }} </br>
                <div class="col-md-5">
                    <?= Form::text('start_date', $todayDate, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) ?>
                </div>
                <div class="col-md-2 text-center">
                    s.d
                </div>
                <div class="col-md-5">
                    <?= Form::text('end_date', $todayDate, ['class' => 'form-control datepicker', 'autocomplete' => 'off']) ?>
                </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-10 col-xs-12">
        <div class="row form-group">
            {{ Form::label('goods', 'Pilih barang yang akan dimasukkan list', ['class' => 'col-md-12']) }} </br>
            @foreach($goods as $good)
            <div class="form-group col-xs-12 col-sm-3">
                <label>{!! Form::checkbox('goods[]', $good->id, false, array('class' => 'goods-list')) !!} {{ $good->name }}</label>
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-6 col-sm-2">
        <a class="btn btn-primary" href="{{ URL::previous() }}" style="width:100%">Back</a>
    </div>
    <div class="form-group col-xs-6 col-sm-2">
        <button type="submit" class="btn btn-success" style="width:100%">Submit</button>
    </div>
    <div class="form-group col-xs-6 col-sm-2">
        <a class="btn btn-danger" style="width:100%" id="btnPdf">Print Pdf</a>
    </div>
</div>

@section('js-include')
<script src="<?= asset('js/backyard/sigarang/price/report.js') ?>"></script>
@endsection
