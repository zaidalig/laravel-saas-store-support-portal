@extends('layouts.dashboard')
@section('page_title','My Tickets')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Ticket or subject" value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Status</label>
            <select name="status" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['open','in_progress','waiting_customer','resolved','closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status')===$status)>{{ str($status)->headline() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Priority</label>
            <select name="priority" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach(['low','medium','high','urgent'] as $priority)
                    <option value="{{ $priority }}" @selected(request('priority')===$priority)>{{ ucfirst($priority) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                @foreach(['created_at'=>'Newest','ticket_number'=>'Ticket','subject'=>'Subject','priority'=>'Priority','status'=>'Status'] as $key=>$label)
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
        <a href="{{ route('dashboard.tickets') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
    <a href="{{ route('dashboard.tickets.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>New Ticket</a>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td><i class="fa-solid fa-headset text-primary me-2"></i>{{ $ticket->ticket_number }}</td>
                    <td>{{ $ticket->subject }}</td>
                    <td><span class="badge bg-warning-subtle text-warning-emphasis border">{{ ucfirst($ticket->priority) }}</span></td>
                    <td><span class="badge bg-primary-subtle text-primary border">{{ str($ticket->status)->headline() }}</span></td>
                    <td class="text-end">
                        <div class="table-actions">
                            <a href="{{ route('dashboard.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="fa-solid fa-eye"></i></a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No tickets found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $tickets->firstItem() ?? 0 }}–{{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }}</div>
        {{ $tickets->onEachSide(1)->links() }}
    </div>
</div>
@endsection
