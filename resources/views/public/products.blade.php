@extends('layouts.public')
@section('title', 'Products')
@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1"><i class="fa-solid fa-box-open text-primary me-2"></i>Products & Services</h1>
            <p class="text-muted mb-0">Browse {{ $products->total() }} available products.</p>
        </div>
    </div>

    <form method="GET" class="panel p-3 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label small fw-semibold"><i class="fa-solid fa-magnifying-glass me-1"></i>Search</label>
                <input name="search" class="form-control" placeholder="Product name or description" value="{{ request('search') }}">
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label small fw-semibold">Category</label>
                <select name="category" class="form-select form-select-compact">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label small fw-semibold">Billing</label>
                <select name="billing_type" class="form-select form-select-compact">
                    <option value="">All</option>
                    @foreach($billingTypes as $type)
                        <option value="{{ $type }}" @selected(request('billing_type') === $type)>{{ str($type)->replace('_', ' ')->title() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label small fw-semibold"><i class="fa-solid fa-arrow-down-wide-short me-1"></i>Sort</label>
                <select name="sort" class="form-select form-select-compact">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest</option>
                    <option value="name_asc" @selected(request('sort') === 'name_asc')>Name A–Z</option>
                    <option value="name_desc" @selected(request('sort') === 'name_desc')>Name Z–A</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Price low–high</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Price high–low</option>
                </select>
            </div>
            <div class="col-sm-6 col-lg-2">
                <label class="form-label small fw-semibold">Per page</label>
                <div class="d-flex gap-2">
                    <select name="per_page" class="form-select form-select-compact">
                        @foreach([9, 18, 36] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', 9) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-primary" title="Apply filters"><i class="fa-solid fa-filter"></i></button>
                    <a href="{{ route('products') }}" class="btn btn-outline-secondary" title="Reset filters"><i class="fa-solid fa-rotate-left"></i></a>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-md-6 col-xl-4">
                <article class="panel p-4 h-100 d-flex flex-column">
                    <span class="badge badge-soft align-self-start mb-3"><i class="fa-solid fa-tag me-1"></i>{{ $product->category?->name ?? 'General' }}</span>
                    <h5 class="fw-bold">{{ $product->name }}</h5>
                    <p class="text-muted flex-grow-1">{{ $product->short_description }}</p>
                    <p class="fw-bold fs-5 mb-3">${{ number_format($product->price, 2) }} <span class="small fw-normal text-muted">/ {{ str($product->billing_type)->replace('_', ' ')->title() }}</span></p>
                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary align-self-start"><i class="fa-solid fa-eye me-1"></i>View details</a>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="panel p-5 text-center text-muted"><i class="fa-solid fa-box-open fa-2x mb-3 d-block"></i>No active products match your filters.</div></div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="pagination-wrap mt-4">
            <div class="small text-muted">Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }}</div>
            {{ $products->onEachSide(1)->links() }}
        </div>
    @endif
</div>
@endsection