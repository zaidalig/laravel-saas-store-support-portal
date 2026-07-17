@extends('layouts.dashboard')
@section('page_title','My Orders')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Order number" value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['pending','confirmed','processing','completed','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Payment</label>
            <select name="payment_status" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['unpaid','partial','paid','refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('payment_status')===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                @foreach(['created_at'=>'Newest','order_number'=>'Order','total'=>'Total','status'=>'Status','payment_status'=>'Payment'] as $key=>$label)
                    <option value="{{ $key }}" @selected(request('sort','created_at')===$key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Per page</label>
            <select name="per_page" class="form-select form-select-compact">
                @foreach([10,25,50] as $size)
                    <option value="{{ $size }}" @selected((int)request('per_page',10)===$size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-dark"><i class="fa-solid fa-filter me-1"></i>Apply</button>
        <a href="{{ route('dashboard.orders') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Total</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td><i class="fa-solid fa-receipt text-primary me-2"></i>{{ $order->order_number }}</td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ ucfirst($order->status) }}</span></td>
                    <td><span class="badge bg-secondary-subtle text-secondary border">{{ ucfirst($order->payment_status) }}</span></td>
                    <td class="fw-semibold">${{ number_format($order->total, 2) }}</td>
                    <td class="text-end">
                        <div class="table-actions">
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }}</div>
        {{ $orders->onEachSide(1)->links() }}
    </div>
</div>
@endsection
