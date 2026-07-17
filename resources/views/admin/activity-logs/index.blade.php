@extends('layouts.admin')
@section('page_title','Activity Logs')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Description, user, module" value="{{ request('search') }}">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Module</label>
            <select name="module" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach($modules as $module)
                    <option value="{{ $module }}" @selected(request('module')===$module)>{{ $module }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Action</label>
            <select name="action" class="form-select form-select-compact">
                <option value="">All</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected(request('action')===$action)>{{ $action }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                @foreach(['created_at'=>'Newest','action'=>'Action','module'=>'Module'] as $key=>$label)
                    <option value="{{ $key }}" @selected(request('sort','created_at')===$key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Per page</label>
            <select name="per_page" class="form-select form-select-compact">
                @foreach([10,25,50,100] as $size)
                    <option value="{{ $size }}" @selected((int)request('per_page',25)===$size)>{{ $size }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-dark"><i class="fa-solid fa-filter me-1"></i>Apply</button>
        <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Module</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>{{ $log->created_at?->format('M d, Y H:i') }}</td>
                    <td>{{ $log->user?->email ?? 'System' }}</td>
                    <td><span class="badge bg-secondary-subtle text-secondary border">{{ $log->action }}</span></td>
                    <td>{{ $log->module }}</td>
                    <td class="text-wrap" style="white-space:normal;min-width:16rem">{{ $log->description }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No activity logs found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }}</div>
        {{ $logs->onEachSide(1)->links() }}
    </div>
</div>
@endsection
