@props(['id', 'name', 'description', 'price'])

<div class="card h-100">
    <div class="card-body">
        <h5 class="card-title">{{ $name }}</h5>
        <p class="card-text text-muted">{{ Str::limit($description, 100) }}</p>
        <p class="card-text">
            <strong class="text-success">Rp {{ number_format($price, 0, ',', '.') }}</strong>
        </p>
    </div>
    <div class="card-footer bg-transparent">
        <div class="d-flex gap-2">
            <a href="{{ route('products.show', $id) }}" class="btn btn-sm btn-info">
                View
            </a>
            <a href="{{ route('products.edit', $id) }}" class="btn btn-sm btn-warning">
                Edit
            </a>
        </div>
    </div>
</div>