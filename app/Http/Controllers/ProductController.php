<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use function view;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:products.index', ['only' => ['index']]);
        $this->middleware('permission:products.show', ['only' => ['show']]);
        $this->middleware('permission:products.create', ['only' => ['create']]);
        $this->middleware('permission:products.store', ['only' => ['store']]);
        $this->middleware('permission:products.edit', ['only' => ['edit']]);
        $this->middleware('permission:products.update', ['only' => ['update']]);
        $this->middleware('permission:products.destroy', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $products = Product::latest()->paginate(5);
        return view('products.index', compact('products'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
    public function create()
    {
        return view('products.create');
    }
    
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
        
        Product::create($request->all());
        
        return redirect()->route('products.index')
            ->with('success', 'Product create successfully');
    }
    
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
    
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }
    
    public function update(Request $request, Product $product)
    {
        request()->validate([
            'name' => 'required',
            'detail' => 'required',
        ]);
        
        $product->update($request->all());
        
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }
    
    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}

