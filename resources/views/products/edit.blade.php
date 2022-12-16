@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Edit product</h2>
        </div>
        <div class="pull-right">
            <a href="{{route('products.index')}}" class="btn btn-primary">Back</a>
        </div>
    </div>
</div>

@if (count($errors) > 0)
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{!! Form::model($product, ['method'=>'PATCH', 'route' => ['products.update', $product->id]]) !!}
<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <strong>Name:</strong>
            {!! Form::text('name', null, array('placeholder' => 'Name', 'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">
            <strong>Detail:</strong>
            {!! Form::text('detail', null, array('placeholder' => 'Detail', 'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>
{!! Form::close() !!}

@endsection

