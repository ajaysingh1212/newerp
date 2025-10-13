<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyAttachVeichleRequest;
use App\Http\Requests\StoreAttachVeichleRequest;
use App\Http\Requests\UpdateAttachVeichleRequest;
use App\Models\ActivationRequest;
use App\Models\AttachVeichle;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class AttachVeichleController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('attach_veichle_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = AttachVeichle::with(['select_user', 'vehicles', 'team'])->select(sprintf('%s.*', (new AttachVeichle)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'attach_veichle_show';
                $editGate      = 'attach_veichle_edit';
                $deleteGate    = 'attach_veichle_delete';
                $crudRoutePart = 'attach-veichles';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->addColumn('select_user_name', function ($row) {
                return $row->select_user ? $row->select_user->name : '';
            });

            $table->editColumn('select_user.email', function ($row) {
                return $row->select_user ? (is_string($row->select_user) ? $row->select_user : $row->select_user->email) : '';
            });
            $table->editColumn('vehicle', function ($row) {
                $labels = [];
                foreach ($row->vehicles as $vehicle) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $vehicle->vehicle_reg_no);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'select_user', 'vehicle']);

            return $table->make(true);
        }

        return view('admin.attachVeichles.index');
    }

    public function create()
    {
        abort_if(Gate::denies('attach_veichle_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicles = ActivationRequest::pluck('vehicle_reg_no', 'id');

        return view('admin.attachVeichles.create', compact('select_users', 'vehicles'));
    }

    public function store(StoreAttachVeichleRequest $request)
    {
        $attachVeichle = AttachVeichle::create($request->all());
        $attachVeichle->vehicles()->sync($request->input('vehicles', []));

        return redirect()->route('admin.attach-veichles.index');
    }

    public function edit(AttachVeichle $attachVeichle)
    {
        abort_if(Gate::denies('attach_veichle_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $vehicles = ActivationRequest::pluck('vehicle_reg_no', 'id');

        $attachVeichle->load('select_user', 'vehicles', 'team');

        return view('admin.attachVeichles.edit', compact('attachVeichle', 'select_users', 'vehicles'));
    }

    public function update(UpdateAttachVeichleRequest $request, AttachVeichle $attachVeichle)
    {
        $attachVeichle->update($request->all());
        $attachVeichle->vehicles()->sync($request->input('vehicles', []));

        return redirect()->route('admin.attach-veichles.index');
    }

    public function show(AttachVeichle $attachVeichle)
    {
        abort_if(Gate::denies('attach_veichle_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attachVeichle->load('select_user', 'vehicles', 'team');

        return view('admin.attachVeichles.show', compact('attachVeichle'));
    }

    public function destroy(AttachVeichle $attachVeichle)
    {
        abort_if(Gate::denies('attach_veichle_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $attachVeichle->delete();

        return back();
    }

    public function massDestroy(MassDestroyAttachVeichleRequest $request)
    {
        $attachVeichles = AttachVeichle::find(request('ids'));

        foreach ($attachVeichles as $attachVeichle) {
            $attachVeichle->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
