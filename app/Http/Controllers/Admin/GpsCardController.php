<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GpsCard;
use App\Models\ProductModel;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class GpsCardController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = GpsCard::query()
                ->leftJoin('product_models', 'gps_cards.product_model_id', '=', 'product_models.id')
                ->leftJoin('users as creators', 'gps_cards.created_by_id', '=', 'creators.id')
                ->leftJoin('users as holders', 'gps_cards.used_by_id', '=', 'holders.id')
                ->select([
                    'gps_cards.*',
                    'product_models.product_model as product_model_name',
                    'creators.name as created_by_name',
                    'holders.name as used_by_name',
                ]);

            $table = DataTables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $deleteUrl = route('admin.gps-cards.destroy', $row->id);
                $showUrl = route('admin.gps-cards.show', $row->id);
                $deleteViewUrl = route('admin.gps-cards.delete', $row->id);
                $printUrl = route('admin.gps-cards.print', $row->id);

                return '
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <a href="' . $showUrl . '" class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . $printUrl . '" class="btn btn-xs btn-primary" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        <a href="' . $deleteViewUrl . '" class="btn btn-xs btn-warning">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                        <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Delete this GPS card?\');">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button class="btn btn-xs btn-danger" type="submit">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                ';
            });

            $table->editColumn('id', fn ($row) => $row->id ?? '');
            $table->editColumn('batch_code', fn ($row) => $row->batch_code ?? '');
            $table->addColumn('product_model', fn ($row) => $row->product_model_name ?? 'N/A');
            $table->editColumn('card_number', fn ($row) => trim(chunk_split((string) $row->card_number, 4, ' ')));
            $table->editColumn('card_holder_name', function ($row) {
                return strtoupper(
                    $row->card_holder_name
                    ?: (\App\Models\User::where('id', $row->used_by_id)->value('name') ?? 'NOT ASSIGNED')
                );
            });
            $table->editColumn('valid_from', fn ($row) => $row->valid_from ? Carbon::parse($row->valid_from)->format('m / Y') : '-- / ----');
            $table->editColumn('valid_to', fn ($row) => $row->valid_to ? Carbon::parse($row->valid_to)->format('m / Y') : '-- / ----');
            $table->addColumn('usage_status', function ($row) {
                if ($row->used_by_activation_request_id) {
                    return '<span class="badge badge-warning px-3 py-2">Used</span>';
                }

                return '<span class="badge badge-info px-3 py-2">Available</span>';
            });
            $table->editColumn('status', function ($row) {
                $validTo = $row->valid_to ? Carbon::parse($row->valid_to) : null;

                if ($row->status === 'inactive') {
                    return '<span class="badge badge-secondary px-3 py-2">Inactive</span>';
                }

                if ($validTo && $validTo->copy()->endOfMonth()->lt(now())) {
                    return '<span class="badge badge-danger px-3 py-2">Expired</span>';
                }

                return '<span class="badge badge-success px-3 py-2">Active</span>';
            });
            $table->addColumn('print_status', function ($row) {
                if ($row->printed_at) {
                    return '<span class="badge badge-success px-3 py-2">Printed</span>';
                }

                return '<span class="badge badge-light border px-3 py-2">Not Printed</span>';
            });
            $table->addColumn('created_by', fn ($row) => $row->created_by_name ?? 'System');

            $table->rawColumns(['actions', 'usage_status', 'status', 'print_status', 'placeholder']);

            return $table->make(true);
        }

        $stats = [
            'total'   => GpsCard::count(),
            'active'  => GpsCard::where('status', 'active')->whereDate('valid_to', '>=', now()->startOfMonth())->count(),
            'expired' => GpsCard::whereDate('valid_to', '<', now()->startOfMonth())->count(),
            'batches' => GpsCard::query()->select('batch_code')->distinct()->count('batch_code'),
        ];

        return view('admin.gpsCards.index', compact('stats'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $productModels = ProductModel::query()
            ->where('status', 'enable')
            ->orderBy('product_model')
            ->get(['id', 'product_model']);

        return view('admin.gpsCards.create', compact('productModels'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'product_model_id' => [
                'required',
                Rule::exists('product_models', 'id'),
            ],
            'valid_from' => ['required', 'date_format:Y-m'],
            'valid_to'   => ['required', 'date_format:Y-m'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:500'],
            'status'     => ['required', Rule::in(array_keys(GpsCard::STATUS_SELECT))],
        ]);

        $validFrom = Carbon::createFromFormat('Y-m', $validated['valid_from'])->startOfMonth();
        $validTo = Carbon::createFromFormat('Y-m', $validated['valid_to'])->startOfMonth();

        if ($validTo->lt($validFrom)) {
            return back()
                ->withErrors(['valid_to' => 'Valid to month must be after or equal to valid from month.'])
                ->withInput();
        }

        $quantity = (int) $validated['quantity'];
        $batchCode = $this->generateBatchCode();

        DB::transaction(function () use ($validated, $validFrom, $validTo, $quantity, $batchCode) {
            $generatedNumbers = [];

            for ($index = 0; $index < $quantity; $index++) {
                $card = GpsCard::create([
                    'batch_code'       => $batchCode,
                    'product_model_id' => $validated['product_model_id'],
                    'card_number'      => $this->generateUniqueCardNumber($generatedNumbers),
                    'valid_from'       => $validFrom->toDateString(),
                    'valid_to'         => $validTo->toDateString(),
                    'status'           => $validated['status'],
                    'created_by_id'    => Auth::id(),
                ]);

                $generatedNumbers[] = $card->card_number;
            }
        });

        return redirect()
            ->route('admin.gps-cards.index')
            ->with('success', $quantity . ' GPS smart cards generated successfully in batch ' . $batchCode . '.');
    }

    public function show(GpsCard $gpsCard)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gpsCard->load(['productModel', 'createdBy', 'usedBy', 'printedBy', 'assignedActivationRequest.select_party', 'assignedActivationRequest.vehicle']);

        $batchCards = GpsCard::query()
            ->where('batch_code', $gpsCard->batch_code)
            ->orderBy('card_number')
            ->get();

        return view('admin.gpsCards.show', compact('gpsCard', 'batchCards'));
    }

    public function print(GpsCard $gpsCard)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gpsCard->load(['productModel', 'createdBy', 'usedBy', 'printedBy', 'assignedActivationRequest.select_party']);

        if (! $gpsCard->printed_at) {
            $gpsCard->update([
                'printed_at'    => now(),
                'printed_by_id' => Auth::id(),
            ]);

            $gpsCard->refresh();
            $gpsCard->load(['productModel', 'createdBy', 'usedBy', 'printedBy', 'assignedActivationRequest.select_party', 'assignedActivationRequest.vehicle']);
        }

        return view('admin.gpsCards.print', compact('gpsCard'));
    }

    public function delete(GpsCard $gpsCard)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gpsCard->load(['productModel', 'createdBy']);

        $batchCount = GpsCard::where('batch_code', $gpsCard->batch_code)->count();

        return view('admin.gpsCards.delete', compact('gpsCard', 'batchCount'));
    }

    public function destroy(GpsCard $gpsCard)
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $gpsCard->delete();

        return redirect()
            ->route('admin.gps-cards.index')
            ->with('success', 'GPS smart card deleted successfully.');
    }

    protected function generateBatchCode(): string
    {
        do {
            $code = 'GPS-' . now()->format('ymd') . '-' . random_int(1000, 9999);
        } while (GpsCard::where('batch_code', $code)->exists());

        return $code;
    }

    protected function generateUniqueCardNumber(array $generatedNumbers = []): string
    {
        do {
            $number = collect(range(1, 4))
                ->map(fn () => str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT))
                ->implode('');
        } while (in_array($number, $generatedNumbers, true) || GpsCard::where('card_number', $number)->exists());

        return $number;
    }
}
