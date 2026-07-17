@extends('layouts.admin')
@section('page_title', $cfg['title'])
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <form class="filter-bar d-flex flex-wrap align-items-end gap-2">
        <div>
            <label class="form-label small fw-semibold mb-1">Search</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input name="search" class="form-control" placeholder="Search records" value="{{ request('search') }}">
            </div>
        </div>
        @foreach(($cfg['filters'] ?? []) as $filter)
            <div>
                <label class="form-label small fw-semibold mb-1">{{ str($filter)->headline() }}</label>
                <input name="{{ $filter }}" class="form-control" value="{{ request($filter) }}" placeholder="All">
            </div>
        @endforeach
        <div>
            <label class="form-label small fw-semibold mb-1">Sort</label>
            <select name="sort" class="form-select form-select-compact">
                <option value="">Newest</option>
                @foreach($allowedSorts as $column)
                    <option value="{{ $column }}" @selected(request('sort') === $column)>{{ str($column)->headline() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Order</label>
            <select name="direction" class="form-select form-select-compact">
                <option value="desc" @selected(request('direction', 'desc') === 'desc')>Descending</option>
                <option value="asc" @selected(request('direction') === 'asc')>Ascending</option>
            </select>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Per page</label>
            <select name="per_page" class="form-select form-select-compact">
                @foreach([10,25,50,100] as $size)<option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>@endforeach
            </select>
        </div>
        <button class="btn btn-dark"><i class="fa-solid fa-filter me-1"></i>Apply</button>
        <a href="{{ route('admin.'.$resource.'.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="fa-solid fa-rotate-left"></i></a>
    </form>
    <a href="{{ route('admin.'.$resource.'.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Add New</a>
</div>

<div class="panel p-3">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr>@foreach($cfg['columns'] as $col)<th>{{ str($col)->after('.')->headline() }}</th>@endforeach<th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse($records as $record)
                <tr>
                    @foreach($cfg['columns'] as $col)
                        <td>
                            @php($value = str_contains($col,'.') ? data_get($record,$col) : $record->{$col})
                            @if(str_contains($col,'status') || str_contains($col,'role'))<span class="badge bg-primary-subtle text-primary border">{{ str($value)->headline() }}</span>@else{{ $value }}@endif
                        </td>
                    @endforeach
                    <td class="text-end">
                        <div class="table-actions">
                            <button class="btn btn-sm btn-outline-secondary" title="View" data-bs-toggle="modal" data-bs-target="#viewModal{{ $record->id }}"><i class="fa-solid fa-eye"></i></button>
                            <a href="{{ route('admin.'.$resource.'.edit',$record->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <button class="btn btn-sm btn-outline-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="{{ route('admin.'.$resource.'.destroy',$record->id) }}"><i class="fa-solid fa-trash"></i></button>
                        </div>
                        <div class="modal fade text-start" id="viewModal{{ $record->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title"><i class="fa-solid fa-eye me-2"></i>{{ $cfg['title'] }} details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body"><dl class="row mb-0">
                                    @foreach($cfg['columns'] as $col)
                                        <dt class="col-5">{{ str($col)->after('.')->headline() }}</dt>
                                        <dd class="col-7">@php($detail = str_contains($col,'.') ? data_get($record,$col) : $record->{$col}){{ $detail ?: '—' }}</dd>
                                    @endforeach
                                </dl></div>
                                <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Close</button><a href="{{ route('admin.'.$resource.'.edit',$record->id) }}" class="btn btn-primary"><i class="fa-solid fa-pen me-1"></i>Edit</a></div>
                            </div></div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ count($cfg['columns'])+1 }}" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-2x d-block mb-2"></i>No records found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap border-top pt-3 mt-3">
        <div class="small text-muted">Showing {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} of {{ $records->total() }}</div>
        {{ $records->onEachSide(1)->links() }}
    </div>
</div>
@endsection