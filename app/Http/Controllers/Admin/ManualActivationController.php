<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualActivation;
use App\Models\ManualActivationDocument;
use App\Models\ManualFitter;
use App\Models\ManualParty;
use App\Models\ManualProduct;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ManualActivationController extends Controller
{
    /**
     * Dashboard-style index with filters (party / fitter / product / state / district / city / this-week)
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('manual_activation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parties = ManualParty::orderBy('name')->get(['id', 'name']);
        $products = ManualProduct::orderBy('name')->get(['id', 'name']);
        $fitters = ManualFitter::orderBy('name')->get(['id', 'name', 'phone']);

        // State/District/City ab manual_parties ke plain string columns hain (koi relation nahi)
        // Cascading filter dropdowns ke liye ek nested map bana lete hain: state -> district -> [cities]
        $locationMap = ManualParty::select('state', 'district', 'city')
            ->whereNotNull('state')
            ->where('state', '!=', '')
            ->get()
            ->groupBy('state')
            ->map(function ($group) {
                return $group->groupBy('district')->map(function ($districtGroup) {
                    return $districtGroup->pluck('city')->filter()->unique()->values();
                });
            });
        $activations = $this->filteredQuery($request)
            ->with(['party', 'fitter', 'product', 'user'])
            ->latest('fitting_date')
            ->paginate(20)
            ->appends($request->query());
        $states = $locationMap->keys()->sort()->values();

        return view('admin.manual-activations.index', compact('activations', 'parties', 'products', 'fitters', 'states', 'locationMap'));
    }

    /**
     * AJAX endpoint that returns chart-ready + table data based on active filters.
     */
    public function chartData(Request $request)
    {
        abort_if(Gate::denies('manual_activation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rows = $this->filteredQuery($request)
            ->with(['party', 'fitter', 'product', 'user'])
            ->latest('fitting_date')
            ->get();

        $groupBy = $request->get('group_by', 'product'); // product | party | fitter | state | district | city

        $grouped = $rows->groupBy(function ($row) use ($groupBy) {
            return match ($groupBy) {
                'party' => $row->party->name ?? 'N/A',
                'fitter' => $row->fitter->name ?? 'N/A',
                'state' => $row->party->state ?? 'N/A',
                'district' => $row->party->district ?? 'N/A',
                'city' => $row->party->city ?? 'N/A',
                default => $row->product->name ?? 'N/A',
            };
        })->map(fn ($group) => $group->count());

        $table = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'fitting_date' => optional($row->fitting_date)->format('d-m-Y'),
                'created_at' => optional($row->created_at)->format('d-m-Y h:i A'),
                'party' => $row->party->name ?? '-',
                'fitter' => $row->fitter->name ?? '-',
                'product' => $row->product->name ?? '-',
                'customer_name' => $row->customer_name ?? '-',
                'customer_phone' => $row->customer_phone ?? '-',
                'state' => $row->party->state ?? '-',
                'district' => $row->party->district ?? '-',
                'city' => $row->party->city ?? '-',
                'vehicle_number' => $row->vehicle_number ?? '-',
                'created_by' => $row->user->name ?? '-',
                'status' => $row->status,
            ];
        });

        return response()->json([
            'labels' => $grouped->keys(),
            'values' => $grouped->values(),
            'total' => $rows->count(),
            'table' => $table,
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        $query = ManualActivation::query();

        if ($request->filled('manual_party_id')) {
            $query->where('manual_party_id', $request->manual_party_id);
        }

        if ($request->filled('manual_fitter_id')) {
            $query->where('manual_fitter_id', $request->manual_fitter_id);
        }

        if ($request->filled('manual_product_id')) {
            $query->where('manual_product_id', $request->manual_product_id);
        }

        if ($request->filled('state') || $request->filled('district') || $request->filled('city')) {
            $query->whereHas('party', function ($q) use ($request) {
                if ($request->filled('state')) {
                    $q->where('state', $request->state);
                }
                if ($request->filled('district')) {
                    $q->where('district', $request->district);
                }
                if ($request->filled('city')) {
                    $q->where('city', $request->city);
                }
            });
        }

        switch ($request->get('range')) {
            case 'today':
                $query->whereDate('fitting_date', Carbon::today());
                break;

            case 'this_week':
                $query->whereBetween('fitting_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;

            case 'this_month':
                $query->whereBetween('fitting_date', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ]);
                break;

            case '3_month':
                $query->whereBetween('fitting_date', [
                    Carbon::now()->subMonths(3)->startOfDay(),
                    Carbon::now()->endOfDay(),
                ]);
                break;

            case '6_month':
                $query->whereBetween('fitting_date', [
                    Carbon::now()->subMonths(6)->startOfDay(),
                    Carbon::now()->endOfDay(),
                ]);
                break;

            case 'this_year':
                $query->whereBetween('fitting_date', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear(),
                ]);
                break;

            case 'custom':
                if ($request->filled('from_date') && $request->filled('to_date')) {
                    $query->whereBetween('fitting_date', [
                        Carbon::parse($request->from_date)->startOfDay(),
                        Carbon::parse($request->to_date)->endOfDay(),
                    ]);
                }
                break;

            case 'all':
            default:
                // koi date filter nahi
                break;
        }

        return $query;
    }

    public function create()
    {
        abort_if(Gate::denies('manual_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parties = ManualParty::orderBy('name')->get(['id', 'name']);
        $products = ManualProduct::orderBy('name')->get(['id', 'name']);
        $fitters = ManualFitter::where('status', 'active')->orderBy('name')->get(['id', 'name', 'phone']);

        return view('admin.manual-activations.create', compact('parties', 'products', 'fitters'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('manual_activation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'manual_party_id' => 'required|exists:manual_parties,id',
            'manual_fitter_id' => 'required|exists:manual_fitters,id',
            'manual_product_id' => 'required|exists:manual_products,id',
            'fitting_date' => 'required|date',

            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',

            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:255',
            'vehicle_chassis_number' => 'nullable|string|max:100',
            'vehicle_engine_number' => 'nullable|string|max:100',
            'vehicle_color' => 'nullable|string|max:50',

            'aadhar_front' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_back' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',

            'document_names' => 'nullable|array',
            'document_names.*' => 'nullable|string|max:255',
            'document_files' => 'nullable|array',
            'document_files.*' => 'nullable|file|max:5120',
        ]);

        $validated['created_by'] = auth()->id();

        if ($request->hasFile('aadhar_front')) {
            $validated['aadhar_front_path'] = $request->file('aadhar_front')
                ->store('manual-activations/aadhar', 'public');
        }

        if ($request->hasFile('aadhar_back')) {
            $validated['aadhar_back_path'] = $request->file('aadhar_back')
                ->store('manual-activations/aadhar', 'public');
        }

        $activation = ManualActivation::create($validated);

        // Dynamic extra documents
        if ($request->has('document_names') && $request->hasFile('document_files')) {
            foreach ($request->document_names as $index => $docName) {
                if (empty($docName) || ! $request->file("document_files.$index")) {
                    continue;
                }

                $path = $request->file("document_files.$index")
                    ->store('manual-activations/documents', 'public');

                ManualActivationDocument::create([
                    'manual_activation_id' => $activation->id,
                    'document_name' => $docName,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.manual-activations.index')
            ->with('status', 'Activation successfully saved / Activation save ho gaya.');
    }

    public function show(ManualActivation $manualActivation)
    {
        abort_if(Gate::denies('manual_activation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualActivation->load(['party', 'fitter', 'product', 'documents']);

        return view('admin.manual-activations.show', compact('manualActivation'));
    }

    public function edit(ManualActivation $manualActivation)
    {
        abort_if(Gate::denies('manual_activation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parties = ManualParty::orderBy('name')->get(['id', 'name']);
        $products = ManualProduct::orderBy('name')->get(['id', 'name']);
        $fitters = ManualFitter::orderBy('name')->get(['id', 'name', 'phone']);
        $manualActivation->load('documents');

        return view('admin.manual-activations.edit', compact('manualActivation', 'parties', 'products', 'fitters'));
    }

    public function update(Request $request, ManualActivation $manualActivation)
    {
        abort_if(Gate::denies('manual_activation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'manual_party_id' => 'required|exists:manual_parties,id',
            'manual_fitter_id' => 'required|exists:manual_fitters,id',
            'manual_product_id' => 'required|exists:manual_products,id',
            'fitting_date' => 'required|date',

            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',

            'vehicle_number' => 'nullable|string|max:50',
            'vehicle_model' => 'nullable|string|max:255',
            'vehicle_chassis_number' => 'nullable|string|max:100',
            'vehicle_engine_number' => 'nullable|string|max:100',
            'vehicle_color' => 'nullable|string|max:50',

            'aadhar_front' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',
            'aadhar_back' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:5120',

            'document_names' => 'nullable|array',
            'document_files' => 'nullable|array',
        ]);

        if ($request->hasFile('aadhar_front')) {
            if ($manualActivation->aadhar_front_path) {
                Storage::disk('public')->delete($manualActivation->aadhar_front_path);
            }
            $validated['aadhar_front_path'] = $request->file('aadhar_front')
                ->store('manual-activations/aadhar', 'public');
        }

        if ($request->hasFile('aadhar_back')) {
            if ($manualActivation->aadhar_back_path) {
                Storage::disk('public')->delete($manualActivation->aadhar_back_path);
            }
            $validated['aadhar_back_path'] = $request->file('aadhar_back')
                ->store('manual-activations/aadhar', 'public');
        }

        $manualActivation->update($validated);

        if ($request->has('document_names') && $request->hasFile('document_files')) {
            foreach ($request->document_names as $index => $docName) {
                if (empty($docName) || ! $request->file("document_files.$index")) {
                    continue;
                }

                $path = $request->file("document_files.$index")
                    ->store('manual-activations/documents', 'public');

                ManualActivationDocument::create([
                    'manual_activation_id' => $manualActivation->id,
                    'document_name' => $docName,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.manual-activations.index')
            ->with('status', 'Activation successfully updated / Activation update ho gaya.');
    }

    public function destroy(ManualActivation $manualActivation)
    {
        abort_if(Gate::denies('manual_activation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualActivation->delete();

        return back()->with('status', 'Activation successfully deleted / Activation delete ho gaya.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('manual_activation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        ManualActivation::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function deleteDocument(ManualActivationDocument $document)
    {
        abort_if(Gate::denies('manual_activation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
