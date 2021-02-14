<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-5">
        <div class="form-group">
            <strong>Name:</strong>
            {!! Form::text('name', null, array('placeholder' => 'Name', 'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-5">
        <div class="form-group">
            <strong>Email:</strong>
            {!! Form::text('email', null, array('placeholder' => 'Email', 'class' => 'form-control')) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-5">
        <div class="form-group">
            <strong>Password:</strong>
            {!! Form::password('password', null, array('class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-5">
        <div class="form-group">
            <strong>Confirm Password:</strong>
            {!! Form::password('confirm-password', null, array('class' => 'form-control')) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-10">
        <div class="form-group">
            <strong>Role:</strong>
            {!! Form::select('roles[]', $roles, isset($userRole) ? $userRole : [], array('class' => 'form-control', 'multiple')) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-5">
        <div class="form-group">
            <strong>Photo:</strong>
            {!! Form::file('photo', array('class' => 'form-control', 'accept' => 'image/*')) !!}
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
</div>

