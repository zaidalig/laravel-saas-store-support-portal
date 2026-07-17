<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->input('per_page', 25), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page', 25)
            : 25;
        $sort = in_array($request->input('sort'), ['created_at', 'action', 'module'], true)
            ? $request->input('sort')
            : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';

        $logs = ActivityLog::with('user')
            ->when($request->search, function ($q, $s) {
                $q->where(function ($nested) use ($s) {
                    $nested->where('description', 'like', "%{$s}%")
                        ->orWhere('action', 'like', "%{$s}%")
                        ->orWhere('module', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($user) => $user->where('email', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"));
                });
            })
            ->when($request->module, fn ($q, $s) => $q->where('module', $s))
            ->when($request->action, fn ($q, $s) => $q->where('action', $s))
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

        $modules = ActivityLog::query()->whereNotNull('module')->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::query()->whereNotNull('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'modules', 'actions'));
    }
}
