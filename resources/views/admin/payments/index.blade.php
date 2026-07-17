@extends('layouts.admin')
@section('page_title','Payments')
@section('content')
<div class="panel p-3 mb-3">
    <form method="POST" action="{{ route('admin.payments.store') }}" class="filter-bar d-flex flex-wrap align-items-end gap-2">
        @csrf
        <div>
            <label class="form-label small fw-semibold mb-1">Order</label>
            <select name="order_id" class="form-select form-select-compact" required>
                @forelse($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->order_number }}</option>
                @empty
                    <option value="" disabled>No orders</option>
                @endforelse
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Amount</label>
            <input name="amount" type="number" step="0.01" class="form-control" style="width:8rem" placeholder="0.00" required>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Method</label>
            <select name="payment_method" class="form-select form-select-compact">
                @foreach(['cash','bank_transfer','card','other'] as $method)
                    <option value="{{ $method }}">{{ str($method)->headline() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-compact">
                @foreach(['pending','completed','failed','refunded'] as $status)
                    <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Paid at</label>
            <input name="paid_at" type="datetime-local" class="form-control">
        </div>
        <button class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Payment</button>
    </form>
</div>

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
                @foreach([10,25,50,100] as $size)
                    <option value="{{ $size }}" @selected((int)request('per_page',10)===$size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-dark"><i class="fa-solid fa-filter me-1"></i>Apply</button>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
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
                    <th>Update</th>
                    <th class="text-end">Actions</th>
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
                    <td>
                        <form method="POST" action="{{ route('admin.payments.update', $payment) }}" class="d-inline-flex align-items-center gap-1">
                            @csrf @method('PUT')
                            <select name="status" class="form-select form-select-sm form-select-compact">
                                @foreach(['pending','completed','failed','refunded'] as $s)
                                    <option value="{{ $s }}" @selected($payment->status===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-sm btn-outline-primary" title="Save status"><i class="fa-solid fa-floppy-disk"></i></button>
                        </form>
                    </td>
                    <td class="text-end">
                        <div class="table-actions">
                            <button class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('admin.payments.destroy', $payment) }}"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No payments found.</td></tr>
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
