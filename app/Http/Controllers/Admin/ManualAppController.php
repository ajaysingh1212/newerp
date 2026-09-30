<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualApp;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ManualAppController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('manual_app_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ManualApp::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $apps = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.manual-apps.index', compact('apps'));
    }

    public function create()
    {
        abort_if(Gate::denies('manual_app_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-apps.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('manual_app_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:manual_apps,name',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        ManualApp::create($validated);

        return redirect()->route('admin.manual-apps.index')->with('status', 'App successfully created.');
    }

    public function show(ManualApp $manualApp)
    {
        abort_if(Gate::denies('manual_app_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-apps.show', compact('manualApp'));
    }

    public function edit(ManualApp $manualApp)
    {
        abort_if(Gate::denies('manual_app_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-apps.edit', compact('manualApp'));
    }

    public function update(Request $request, ManualApp $manualApp)
    {
        abort_if(Gate::denies('manual_app_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('manual_apps', 'name')->ignore($manualApp->id)],
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $manualApp->update($validated);

        return redirect()->route('admin.manual-apps.index')->with('status', 'App successfully updated.');
    }

    public function destroy(ManualApp $manualApp)
    {
        abort_if(Gate::denies('manual_app_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualApp->delete();

        return back()->with('status', 'App successfully deleted.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('manual_app_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:manual_apps,id']);
        ManualApp::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
