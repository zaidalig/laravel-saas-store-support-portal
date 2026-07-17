@extends('layouts.admin')
@section('page_title','Subscriptions')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Subscription, customer, plan" value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['active','cancelled','expired'] as $status)
                    <option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                @foreach(['created_at'=>'Newest','subscription_number'=>'Subscription','status'=>'Status','starts_at'=>'Starts','ends_at'=>'Ends'] as $key=>$label)
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
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Subscription</th>
                    <th>Customer</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Starts</th>
                    <th>Ends</th>
                    <th>Cancelled</th>
                </tr>
            </thead>
            <tbody>
            @forelse($subscriptions as $subscription)
                <tr>
                    <td><i class="fa-solid fa-arrows-rotate text-primary me-2"></i>{{ $subscription->subscription_number }}</td>
                    <td>{{ $subscription->user->name }}<div class="small text-muted">{{ $subscription->user->email }}</div></td>
                    <td>{{ $subscription->pricingPlan->name }}</td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ ucfirst($subscription->status) }}</span></td>
                    <td>{{ $subscription->starts_at->format('M d, Y') }}</td>
                    <td>{{ $subscription->ends_at->format('M d, Y') }}</td>
                    <td>{{ $subscription->cancelled_at?->format('M d, Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No subscriptions found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $subscriptions->firstItem() ?? 0 }}–{{ $subscriptions->lastItem() ?? 0 }} of {{ $subscriptions->total() }}</div>
        {{ $subscriptions->onEachSide(1)->links() }}
    </div>
</div>
@endsection
