@extends('layouts.dashboard')
@section('page_title','My Payments')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Payment or order" value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['pending','completed','failed','refunded'] as $status)
                    <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Method</label>
            <select name="payment_method" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['cash','bank_transfer','card','other'] as $method)
                    <option value="{{ $method }}" @selected(request('payment_method')===$method)>{{ str($method)->headline() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                @foreach(['created_at'=>'Newest','payment_number'=>'Payment','amount'=>'Amount','status'=>'Status','payment_method'=>'Method'] as $key=>$label)
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
        <a href="{{ route('dashboard.payments') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Payment</th>
                    <th>Order</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td><i class="fa-solid fa-credit-card text-primary me-2"></i>{{ $payment->payment_number }}</td>
                    <td>{{ $payment->order?->order_number ?? '—' }}</td>
                    <td>{{ str($payment->payment_method)->headline() }}</td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ ucfirst($payment->status) }}</span></td>
                    <td class="fw-semibold">${{ number_format($payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No payments found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} of {{ $payments->total() }}</div>
        {{ $payments->onEachSide(1)->links() }}
    </div>
</div>
@endsection
