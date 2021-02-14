@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Products</h2>
        </div>
        <div class="pull-right">
            @can('products.create')
            <a href="{{route('products.create')}}" class="btn btn-success">Create New Prodcut</a>
            @endcan
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    <p>{{$message}}</p>
</div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th width='280px'>Action</th>
    </tr>
    @foreach ($products as $key => $product)
    <tr>
        <td>{{ ++$i }}</td>
        <td>{{ $product->name }}</td>
        <td>
            <a class="btn btn-info" href="{{ route('products.show', $product->id) }}">Show</a>
            @can('product-edit')
            <a class="btn btn-primary" href="{{ route('products.edit', $product->id) }}">Edit</a>
            @endcan
            @can('product-delete')
            {!! Form::open(['method' => 'DELETE', 'route' => ['products.destroy', $product->id], 'style' => 'display:inline']) !!}
            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
            @endcan
        </td>
    </tr>
    @endforeach
</table>

{!! $products->render() !!}
@endsection
