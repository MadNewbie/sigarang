<div class="row">
    <div class="form-group col-xs-12 col-sm-4">
        {!! Form::label('province_id', 'Provinsi') !!}
        {!! Form::select('province_id', $provinceOptions, null, array('class' => 'form-control')) !!}
    </div>
</div>
<div class="row">
    <div class="form-group col-xs-12 col-sm-4">
        {!! Form::label('name', 'Nama') !!}
        {!! Form::text('name', null, array('placeholder' => 'Name', 'class' => 'form-control')) !!}
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
