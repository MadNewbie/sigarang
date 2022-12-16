<div class="row">
    <div class="form-group col-xs-12 col-sm-4">
        {!! Form::label('name','Name') !!}
        {!! Form::text('name', null, array('placeholder' => 'Name', 'class' => 'form-control')) !!}
    </div>
    <div class="form-group col-xs-12 col-sm-12">
        {!! Form::label('name','Permission') !!}</br>
        @foreach($permission as $value)
        <label>{!! Form::checkbox('permissions[]', $value->id, isset($rolePermissions) ? in_array($value->id, $rolePermissions) ? true : false : false, array('class' => 'name')) !!} {{ $value->name }}</label>
        <br/>
        @endforeach
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

