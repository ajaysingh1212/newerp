<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualFitter;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ManualFitterController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('manual_fitter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ManualFitter::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('whatsapp_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $fitters = $query->latest()->paginate(20)->appends($request->query());

        $states = ManualFitter::whereNotNull('state')->where('state', '!=', '')
            ->distinct()->orderBy('state')->pluck('state');

        return view('admin.manual-fitters.index', compact('fitters', 'states'));
    }

    public function create()
    {
        abort_if(Gate::denies('manual_fitter_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-fitters.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('manual_fitter_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $this->validateFitter($request);

        $validated['created_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('manual-fitters/photo', 'public');
        }

        if ($request->hasFile('id_proof')) {
            $validated['id_proof_path'] = $request->file('id_proof')->store('manual-fitters/id-proof', 'public');
        }

        ManualFitter::create($validated);

        return redirect()->route('admin.manual-fitters.index')
            ->with('status', 'Fitter successfully saved / Fitter save ho gaya.');
    }

    public function show(ManualFitter $manualFitter)
    {
        abort_if(Gate::denies('manual_fitter_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-fitters.show', compact('manualFitter'));
    }

    public function edit(ManualFitter $manualFitter)
    {
        abort_if(Gate::denies('manual_fitter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-fitters.edit', compact('manualFitter'));
    }

    public function update(Request $request, ManualFitter $manualFitter)
    {
        abort_if(Gate::denies('manual_fitter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $this->validateFitter($request, $manualFitter->id);

        if ($request->hasFile('photo')) {
            if ($manualFitter->photo_path) {
                Storage::disk('public')->delete($manualFitter->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('manual-fitters/photo', 'public');
        }

        if ($request->hasFile('id_proof')) {
            if ($manualFitter->id_proof_path) {
                Storage::disk('public')->delete($manualFitter->id_proof_path);
            }
            $validated['id_proof_path'] = $request->file('id_proof')->store('manual-fitters/id-proof', 'public');
        }

        $manualFitter->update($validated);

        return redirect()->route('admin.manual-fitters.index')
            ->with('status', 'Fitter successfully updated / Fitter update ho gaya.');
    }

    public function destroy(ManualFitter $manualFitter)
    {
        abort_if(Gate::denies('manual_fitter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualFitter->delete();

        return back()->with('status', 'Fitter successfully deleted / Fitter delete ho gaya.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('manual_fitter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        ManualFitter::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    protected function validateFitter(Request $request, $id = null)
    {
        return $request->validate([
            // ---- Basic Details ----
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // ---- Contact Details (all optional) ----
            'alternate_phone' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'aadhar_number' => 'nullable|string|max:20',
            'id_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'address' => 'nullable|string',
            'landmark' => 'nullable|string|max:255',

            // ---- Address Details (required) ----
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',

            'status' => 'nullable|in:active,inactive',
        ]);
    }
}
