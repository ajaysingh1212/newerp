<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualParty;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManualPartyController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('manual_party_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ManualParty::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('gst_number', 'like', "%{$s}%");
            });
        }

        $parties = $query->latest()->paginate(15);

        return view('admin.manual-parties.index', compact('parties'));
    }

    public function create()
    {
        abort_if(Gate::denies('manual_party_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-parties.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('manual_party_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:30',
            'ifsc_code' => 'nullable|string|max:20',
            'branch_name' => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = auth()->id();

        ManualParty::create($validated);

        return redirect()->route('admin.manual-parties.index')
            ->with('status', 'Party successfully created / Party safaltapoorvak ban gaya.');
    }

    public function edit(ManualParty $manualParty)
    {
        abort_if(Gate::denies('manual_party_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-parties.edit', compact('manualParty'));
    }

    public function update(Request $request, ManualParty $manualParty)
    {
        abort_if(Gate::denies('manual_party_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'state' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'gst_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:30',
            'ifsc_code' => 'nullable|string|max:20',
            'branch_name' => 'nullable|string|max:255',
        ]);

        $manualParty->update($validated);

        return redirect()->route('admin.manual-parties.index')
            ->with('status', 'Party successfully updated / Party update ho gaya.');
    }

    public function show(ManualParty $manualParty)
    {
        abort_if(Gate::denies('manual_party_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualParty->load(['activations.product']);

        return view('admin.manual-parties.show', compact('manualParty'));
    }

    public function destroy(ManualParty $manualParty)
    {
        abort_if(Gate::denies('manual_party_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualParty->delete();

        return back()->with('status', 'Party successfully deleted / Party delete ho gaya.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('manual_party_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        ManualParty::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
