<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BusinessReportExport;
use App\Http\Controllers\Controller;
use App\Imports\BusinessDevicesImport;
use App\Models\BusinessDevice;
use App\Models\ManualParty;
use App\Models\ManualProduct;
use App\Services\BusinessDeviceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BusinessManagementController extends Controller
{
    private function authorizeAction(string $action): void
    {
        Gate::authorize('business_management_'.$action);
    }

    public function index(Request $request)
    {
        $this->authorizeAction('access');
        $parties = ManualParty::orderBy('name')->get();
        $partyId = $request->input('party_id', $parties->first()?->id);
        $request->validate(['party_id' => ['nullable', 'integer', Rule::exists('manual_parties', 'id')->whereNull('deleted_at')], 'status' => ['nullable', Rule::in(['pending', 'activated'])]]);
        $query = BusinessDevice::where('business_devices.manual_party_id', $partyId);
        if ($request->filled('status')) {
            $query->where('business_devices.status', $request->status);
        }
        if ($request->ajax()) {
            return $this->deviceTable($query, true);
        }
        $total = (clone $query)->sum('sales_price');
        $counts = (clone $query)->selectRaw("COUNT(*) as devices, SUM(CASE WHEN status = 'activated' THEN 1 ELSE 0 END) as activated")->first();

        return view('admin.business-management.index', compact('parties', 'partyId', 'counts', 'total'));
    }

    private function deviceTable($query, bool $actions = false)
    {
        $table = DataTables::eloquent($query->select('business_devices.*')->with(['party', 'product', 'activator']))
            ->makeHidden(['party', 'product', 'activator'])
            ->addColumn('model_name', fn ($device) => $device->product?->name ?? '')
            ->addColumn('party_name', fn ($device) => $device->party?->name ?? '')
            ->editColumn('assigned_at', fn ($device) => $device->assigned_at?->format('Y-m-d'))
            ->addColumn('location', fn ($device) => implode(' / ', array_filter([$device->party?->state, $device->party?->district, $device->party?->city])))
            ->addColumn('activation', fn ($device) => trim(($device->activator?->name ?? '').' '.($device->activated_at?->format('Y-m-d H:i:s') ?? '')));
        if ($actions) {
            $table->addColumn('actions', fn ($device) => view('admin.business-management.actions', compact('device'))->render())->rawColumns(['actions']);
        }

        return $table->only(['id', 'model_name', 'party_name', 'vts_number', 'imei_number', 'sim_number', 'sales_price', 'assigned_at', 'status', 'location', 'activation', 'actions'])->escapeColumns([])->toJson();
    }

    public function create()
    {
        $this->authorizeAction('create');

        return $this->form();
    }

    private function form(?BusinessDevice $businessDevice = null)
    {
        $parties = ManualParty::orderBy('name')->get();
        $products = ManualProduct::orderBy('name')->get();

        return view('admin.business-management.form', compact('parties', 'products', 'businessDevice'));
    }

    public function store(Request $request, BusinessDeviceService $service)
    {
        $this->authorizeAction('create');
        $this->decodeRows($request);
        $data = $request->validate($service->rules((int) $request->input('quantity')));
        $service->store($data, auth()->id());

        return redirect()->route('admin.business-management.index', ['party_id' => $data['manual_party_id']])->with('status', 'Devices added successfully.');
    }

    public function show(BusinessDevice $businessDevice)
    {
        $this->authorizeAction('show');
        $businessDevice->load(['party', 'product', 'activator']);

        return view('admin.business-management.show', compact('businessDevice'));
    }

    public function edit(BusinessDevice $businessDevice)
    {
        $this->authorizeAction('edit');

        return $this->form($businessDevice);
    }

    public function update(Request $request, BusinessDevice $businessDevice, BusinessDeviceService $service)
    {
        $this->authorizeAction('edit');
        $this->decodeRows($request);
        $data = $request->validate($service->rules(1, $businessDevice));
        abort_unless((int) $data['quantity'] === 1, 422);
        $businessDevice->update(array_merge($data['devices'][0], ['manual_party_id' => $data['manual_party_id'], 'assigned_at' => $data['assigned_at']]));

        return redirect()->route('admin.business-management.index', ['party_id' => $data['manual_party_id']])->with('status', 'Device updated.');
    }

    private function decodeRows(Request $request): void
    {
        if ($request->has('devices_payload')) {
            $request->validate(['devices_payload' => ['required', 'string', 'json', 'max:1048576']]);
            $request->merge(['devices' => json_decode($request->input('devices_payload'), true)]);
        }
    }

    public function destroy(BusinessDevice $businessDevice)
    {
        $this->authorizeAction('delete');
        $partyId = $businessDevice->manual_party_id;
        $businessDevice->delete();

        return redirect()->route('admin.business-management.index', ['party_id' => $partyId])->with('status', 'Device deleted.');
    }

    public function activate(BusinessDevice $businessDevice)
    {
        $this->authorizeAction('activate');
        $changed = BusinessDevice::whereKey($businessDevice->id)->where('status', 'pending')->update([
            'status' => 'activated', 'activated_by' => auth()->id(), 'activated_at' => now(),
        ]);

        return back()->with('status', $changed ? 'Device activated successfully.' : 'Device is already activated.');
    }

    public function importForm()
    {
        $this->authorizeAction('import');
        $parties = ManualParty::orderBy('name')->get();
        $products = ManualProduct::orderBy('name')->get();

        return view('admin.business-management.import', compact('parties', 'products'));
    }

    public function template()
    {
        $this->authorizeAction('import');

        return response()->streamDownload(function () {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['manual_party_id', 'manual_product_id', 'vts_number', 'sim_number', 'sales_price']);
            fclose($stream);
        }, 'business-devices-template.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request, BusinessDeviceService $service)
    {
        $this->authorizeAction('import');
        $request->validate(['mapping' => ['sometimes', 'required', 'array'], 'party_source' => ['sometimes', 'required', Rule::in(['file', 'single'])]]);
        $import = $this->readImport($request);
        $mapping = $request->input('mapping');
        $perRowParty = $request->input('party_source', isset(($mapping ?? $import->suggestedMapping())['manual_party_id']) ? 'file' : 'single') === 'file';
        $devices = $import->devices($mapping, $perRowParty);
        $data = Validator::make([
            'manual_party_id' => $request->input('manual_party_id'), 'assigned_at' => $request->input('assigned_at'),
            'quantity' => count($devices), 'devices' => $devices,
        ], $service->importRules(count($devices), $perRowParty))->validate();
        $service->store($data, auth()->id());
        $partyIds = $perRowParty ? array_unique(array_column($devices, 'manual_party_id')) : [$data['manual_party_id']];
        $message = count($devices).' devices imported successfully across '.count($partyIds).' parties.';
        $redirect = route('admin.business-management.index', ['party_id' => reset($partyIds)]);

        if ($request->expectsJson()) {
            $request->session()->flash('status', $message);

            return response()->json(['redirect' => $redirect, 'devices' => count($devices), 'parties' => count($partyIds)]);
        }

        return redirect($redirect)->with('status', $message);
    }

    public function importPreview(Request $request)
    {
        $this->authorizeAction('import');
        $import = $this->readImport($request);

        return response()->json([
            'headers' => $import->headers, 'rows' => $import->rows,
            'mapping' => $import->suggestedMapping(), 'count' => count($import->rows),
        ]);
    }

    private function readImport(Request $request): BusinessDevicesImport
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120']]);
        $import = new BusinessDevicesImport;
        try {
            Excel::import($import, $request->file('file'));
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages(['file' => 'Unable to read this file. Please use the provided CSV headers or a valid Excel workbook.']);
        }

        return $import;
    }

    public function report(Request $request)
    {
        $this->authorizeAction('report');
        $request->validate([
            'party_id' => ['nullable', 'integer'], 'product_id' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::in(['pending', 'activated'])],
            'from' => ['nullable', 'date_format:Y-m-d'], 'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'state' => ['nullable', 'string', 'max:255'], 'district' => ['nullable', 'string', 'max:255'], 'city' => ['nullable', 'string', 'max:255'],
            'group_by' => ['nullable', Rule::in(['party', 'model', 'state', 'district', 'city'])],
            'export' => ['nullable', Rule::in(['xlsx', 'csv'])],
            'date_range' => ['nullable', Rule::in(['all', 'today', 'week', 'month', 'year', 'custom'])],
        ]);
        $dateRange = $request->input('date_range', $request->filled('from') || $request->filled('to') ? 'custom' : 'all');
        if (in_array($dateRange, ['today', 'week', 'month', 'year'], true)) {
            $today = Carbon::now('Asia/Kolkata')->startOfDay();
            [$from, $to] = match ($dateRange) {
                'today' => [$today->copy(), $today->copy()],
                'week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
                'month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
                default => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            };
            $request->merge(['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]);
        } elseif ($dateRange === 'all' && $request->has('date_range')) {
            $request->merge(['from' => null, 'to' => null]);
        }
        $query = BusinessDevice::query();
        foreach (['party_id' => 'manual_party_id', 'product_id' => 'manual_product_id', 'status' => 'status'] as $filter => $column) {
            if ($request->filled($filter)) {
                $query->where('business_devices.'.$column, $request->input($filter));
            }
        }
        foreach (['state', 'district', 'city'] as $location) {
            if ($request->filled($location)) {
                $query->whereHas('party', fn ($q) => $q->where($location, $request->input($location)));
            }
        }
        if ($request->filled('from')) {
            $query->where('business_devices.assigned_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('business_devices.assigned_at', '<', Carbon::parse($request->input('to'))->addDay()->format('Y-m-d'));
        }
        if ($request->filled('export')) {
            return Excel::download(new BusinessReportExport(clone $query), 'business-report.'.$request->input('export'));
        }
        if ($request->ajax()) {
            return $this->deviceTable($query);
        }
        $totals = (clone $query)->selectRaw("COUNT(*) as devices, COALESCE(SUM(sales_price), 0) as sales, COALESCE(SUM(CASE WHEN status = 'activated' THEN 1 ELSE 0 END), 0) as activated")->first();
        $groupBy = $request->input('group_by') ?: 'model';
        $columns = match ($groupBy) {
            'model' => ['manual_products.id', 'manual_products.name'],
            'state' => ['manual_parties.state'],
            'district' => ['manual_parties.state', 'manual_parties.district'],
            'city' => ['manual_parties.state', 'manual_parties.district', 'manual_parties.city'],
            default => ['manual_parties.id', 'manual_parties.name'],
        };
        $summaryQuery = DB::table('business_devices')
            ->join('manual_parties', 'manual_parties.id', '=', 'business_devices.manual_party_id')
            ->leftJoin('manual_products', 'manual_products.id', '=', 'business_devices.manual_product_id')
            ->whereIn('business_devices.id', (clone $query)->select('business_devices.id'));
        $labelColumns = array_values(array_filter($columns, fn ($column) => ! str_ends_with($column, '.id')));
        foreach ($labelColumns as $index => $column) {
            $summaryQuery->addSelect($column.' as label_'.$index);
        }
        $summaries = $summaryQuery->selectRaw("COUNT(*) as devices, SUM(business_devices.sales_price) as sales, SUM(CASE WHEN business_devices.status = 'activated' THEN 1 ELSE 0 END) as activated")
            ->groupBy(...$columns)->orderBy($columns[0])->get();
        $reportStats = [
            'parties' => (clone $query)->distinct()->count('business_devices.manual_party_id'),
            'products' => (clone $query)->distinct()->count('business_devices.manual_product_id'),
            'states' => ManualParty::withTrashed()->whereIn('id', (clone $query)->select('business_devices.manual_party_id'))->whereNotNull('state')->whereRaw("TRIM(state) <> ''")->distinct()->count('state'),
        ];
        $partyProducts = DB::table('business_devices')
            ->join('manual_parties', 'manual_parties.id', '=', 'business_devices.manual_party_id')
            ->leftJoin('manual_products', 'manual_products.id', '=', 'business_devices.manual_product_id')
            ->whereIn('business_devices.id', (clone $query)->select('business_devices.id'))
            ->select(['manual_parties.id as party_id', 'manual_parties.name as party_name', 'manual_products.id as product_id', 'manual_products.name as product_name'])
            ->selectRaw("COUNT(*) as devices, SUM(CASE WHEN business_devices.status = 'activated' THEN 1 ELSE 0 END) as activated, SUM(business_devices.sales_price) as sales")
            ->groupBy('manual_parties.id', 'manual_parties.name', 'manual_products.id', 'manual_products.name')
            ->orderBy('manual_parties.name')->orderBy('manual_products.name')->get();
        $reportLocations = ManualParty::select('state', 'district', 'city')->distinct()->get();
        $parties = ManualParty::orderBy('name')->get();
        $products = ManualProduct::orderBy('name')->get();

        $months = [];
        $undated = 0;
        $missingPrices = 0;
        foreach ((clone $query)->select(['business_devices.id', 'assigned_at', 'status', 'sales_price'])->orderBy('assigned_at')->orderBy('business_devices.id')->cursor() as $device) {
            if ($device->sales_price === null) {
                $missingPrices++;
            }
            if ($device->assigned_at === null) {
                $undated++;

                continue;
            }
            $month = $device->assigned_at->format('Y-m');
            $months[$month] ??= ['month' => $month, 'devices' => 0, 'activated' => 0, 'pending' => 0, 'sales_cents' => 0, 'open' => null, 'close' => null, 'low' => null, 'high' => null];
            $months[$month]['devices']++;
            $months[$month][$device->status === 'activated' ? 'activated' : 'pending']++;
            if ($device->sales_price !== null) {
                $price = (float) $device->sales_price;
                $months[$month]['sales_cents'] += (int) round($price * 100);
                $months[$month]['open'] ??= $price;
                $months[$month]['close'] = $price;
                $months[$month]['low'] = $months[$month]['low'] === null ? $price : min($price, $months[$month]['low']);
                $months[$month]['high'] = $months[$month]['high'] === null ? $price : max($price, $months[$month]['high']);
            }
        }
        if ($months) {
            $cursor = Carbon::parse(array_key_first($months).'-01')->startOfMonth();
            $last = Carbon::parse(array_key_last($months).'-01')->startOfMonth();
            $continuous = [];
            while ($cursor->lte($last)) {
                $month = $cursor->format('Y-m');
                $continuous[$month] = $months[$month] ?? ['month' => $month, 'devices' => 0, 'activated' => 0, 'pending' => 0, 'sales_cents' => 0, 'open' => null, 'close' => null, 'low' => null, 'high' => null];
                $cursor->addMonth();
            }
            $months = $continuous;
        }
        $reportChartData = [
            'groups' => $summaries->map(fn ($row) => [
                'label' => implode(' / ', array_map(fn ($index) => trim((string) $row->{'label_'.$index}) ?: 'Not supplied', array_keys($labelColumns))),
                'devices' => (int) $row->devices, 'activated' => (int) $row->activated,
                'pending' => (int) $row->devices - (int) $row->activated, 'sales' => (float) ($row->sales ?? 0),
            ])->values(),
            'months' => array_values(array_map(function ($month) {
                $month['sales'] = $month['sales_cents'] / 100;
                unset($month['sales_cents']);

                return $month;
            }, $months)),
            'undated' => $undated, 'missingPrices' => $missingPrices, 'groupBy' => $groupBy,
            'locations' => $reportLocations, 'exactGroups' => true, 'rankedLegend' => true,
        ];

        return view('admin.business-management.report', compact('totals', 'summaries', 'parties', 'products', 'groupBy', 'labelColumns', 'reportChartData', 'reportStats', 'partyProducts', 'reportLocations', 'dateRange'));
    }
}
