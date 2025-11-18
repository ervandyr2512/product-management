@extends('layout')

@section('title', 'Product List')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1>Product List</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            Add new product
        </a>
    </div>
</div>

<div class="row">
    @forelse($products as $product)
        <div class="col-md-6 col-lg-4 mb-4">
            <x-product-card 
                :id="$product['id']"
                :name="$product['name']"
                :description="$product['description']"
                :price="$product['price']"
            />
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info">
                No products available.
            </div>
        </div>
    @endforelse
</div>

<div class="row mt-4">
    <div class="col-12">
        <p class="text-muted">
            Showing {{ count($products) }} products
        </p>
    </div>
</div>
@endsection