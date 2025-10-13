<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyDistrictRequest;
use App\Http\Requests\StoreDistrictRequest;
use App\Http\Requests\UpdateDistrictRequest;
use App\Models\District;
use App\Models\State;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class DistrictsController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('district_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = District::with(['select_state', 'team'])->select(sprintf('%s.*', (new District)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'district_show';
                $editGate      = 'district_edit';
                $deleteGate    = 'district_delete';
                $crudRoutePart = 'districts';

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
            $table->editColumn('districts', function ($row) {
                return $row->districts ? $row->districts : '';
            });
            $table->editColumn('country', function ($row) {
                return $row->country ? $row->country : '';
            });
            $table->addColumn('select_state_state_name', function ($row) {
                return $row->select_state ? $row->select_state->state_name : '';
            });

            $table->editColumn('select_state.country', function ($row) {
                return $row->select_state ? (is_string($row->select_state) ? $row->select_state : $row->select_state->country) : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'select_state']);

            return $table->make(true);
        }

        return view('admin.districts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('district_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.districts.create', compact('select_states'));
    }

    public function store(StoreDistrictRequest $request)
    {
        $district = District::create($request->all());

        return redirect()->route('admin.districts.index');
    }

    public function edit(District $district)
    {
        abort_if(Gate::denies('district_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_states = State::pluck('state_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $district->load('select_state', 'team');

        return view('admin.districts.edit', compact('district', 'select_states'));
    }

    public function update(UpdateDistrictRequest $request, District $district)
    {
        $district->update($request->all());

        return redirect()->route('admin.districts.index');
    }

    public function show(District $district)
    {
        abort_if(Gate::denies('district_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $district->load('select_state', 'team', 'disrictActivationRequests');

        return view('admin.districts.show', compact('district'));
    }

    public function destroy(District $district)
    {
        abort_if(Gate::denies('district_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $district->delete();

        return back();
    }

    public function massDestroy(MassDestroyDistrictRequest $request)
    {
        $districts = District::find(request('ids'));

        foreach ($districts as $district) {
            $district->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
