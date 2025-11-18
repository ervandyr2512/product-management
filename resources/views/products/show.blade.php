@extends('layout')

@section('title', 'Product Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-3">
            <a href="{{ route('products') }}" class="btn btn-secondary">
                ← Back to Products
            </a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Product Details</h2>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h3>{{ $product['name'] }}</h3>
                    <p class="text-muted">Product ID: #{{ $product['id'] }}</p>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p>{{ $product['description'] }}</p>
                </div>

                <div class="mb-4">
                    <h5>Price</h5>
                    <p class="fs-4 text-success fw-bold">
                        Rp {{ number_format($product['price'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('products.edit', $product['id']) }}" class="btn btn-warning">
                        Edit Product
                    </a>
                    <a href="{{ route('products') }}" class="btn btn-outline-secondary">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection