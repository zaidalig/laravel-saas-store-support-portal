@extends('layouts.admin')
@section('page_title','Invoices')
@section('content')
<div class="panel p-3 mb-3">
    <form method="POST" action="{{ route('admin.invoices.store') }}" class="filter-bar d-flex flex-wrap align-items-end gap-2">
        @csrf
        <div>
            <label class="form-label small fw-semibold mb-1">Order without invoice</label>
            <select name="order_id" class="form-select form-select-compact" required @disabled($orders->isEmpty())>
                @forelse($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->order_number }} — {{ $order->customer_name }}</option>
                @empty
                    <option value="">All orders invoiced</option>
                @endforelse
            </select>
        </div>
        <button class="btn btn-primary" @disabled($orders->isEmpty())><i class="fa-solid fa-file-invoice me-1"></i>Generate Invoice</button>
    </form>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Invoice or order" value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['unpaid','partial','paid','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                @foreach(['created_at'=>'Newest','invoice_number'=>'Invoice','total'=>'Total','status'=>'Status','issue_date'=>'Issue date','due_date'=>'Due date'] as $key=>$label)
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
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->order?->order_number ?? '—' }}</td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ ucfirst($invoice->status) }}</span></td>
                    <td class="fw-semibold">${{ number_format($invoice->total, 2) }}</td>
                    <td class="text-end">
                        <div class="table-actions">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('admin.invoices.print', $invoice) }}" class="btn btn-sm btn-outline-primary" title="Print" target="_blank"><i class="fa-solid fa-print"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No invoices found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }}</div>
        {{ $invoices->onEachSide(1)->links() }}
    </div>
</div>
@endsection
