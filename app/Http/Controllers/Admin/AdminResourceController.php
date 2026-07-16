<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminResourceController extends Controller
{
    private function cfg(Request $request): array
    {
        return config('admin_resources.'.$request->route('resource')) ?? abort(404);
    }

    public function index(Request $request)
    {
        $cfg = $this->cfg($request);
        $query = $cfg['model']::query();
        foreach (($cfg['relations'] ?? []) as $relation) {
            $query->with($relation);
        }
        if ($request->filled('search') && ! empty($cfg['search'])) {
            $search = $request->search;
            $query->where(function ($q) use ($cfg, $search) {
                foreach ($cfg['search'] as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            });
        }
        foreach (($cfg['filters'] ?? []) as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }
        return view('admin.crud.index', [
            'cfg' => $cfg,
            'resource' => $request->route('resource'),
            'records' => $query->latest()->paginate(10)->withQueryString(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.crud.form', ['cfg' => $this->cfg($request), 'resource' => $request->route('resource'), 'record' => null]);
    }

    public function store(Request $request)
    {
        $cfg = $this->cfg($request);
        $data = $request->validate($cfg['rules']);
        if ($request->route('resource') === 'users') {
            $data['password'] = $data['password'] ?: 'password';
        }
        $record = $cfg['model']::create($data);
        ActivityLogger::model('Created', $record, $record->{$cfg['label']} ?? 'record');
        return redirect()->route('admin.resource.index', $request->route('resource'))->with('success', $cfg['title'].' record created.');
    }

    public function edit(Request $request, int $id)
    {
        $cfg = $this->cfg($request);
        return view('admin.crud.form', ['cfg' => $cfg, 'resource' => $request->route('resource'), 'record' => $cfg['model']::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $cfg = $this->cfg($request);
        $rules = $cfg['rules'];
        if ($request->route('resource') === 'users') {
            $rules['email'] = ['required','email','max:255', Rule::unique('users', 'email')->ignore($id)];
        }
        $data = $request->validate($rules);
        if ($request->route('resource') === 'users' && blank($data['password'])) {
            unset($data['password']);
        }
        $record = $cfg['model']::findOrFail($id);
        $record->update($data);
        ActivityLogger::model('Updated', $record, $record->{$cfg['label']} ?? 'record');
        return redirect()->route('admin.resource.index', $request->route('resource'))->with('success', $cfg['title'].' record updated.');
    }

    public function destroy(Request $request, int $id)
    {
        $cfg = $this->cfg($request);
        $record = $cfg['model']::findOrFail($id);
        if ($record instanceof User && $record->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        $label = $record->{$cfg['label']} ?? 'record';
        $record->delete();
        ActivityLogger::log('Deleted', class_basename($cfg['model']), "Deleted {$label}");
        return back()->with('success', $cfg['title'].' record deleted.');
    }
}