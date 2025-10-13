<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserAlertRequest;
use App\Http\Requests\StoreUserAlertRequest;
use App\Models\User;
use App\Models\UserAlert;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;


class UserAlertsController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('user_alert_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = UserAlert::with(['users'])->select(sprintf('%s.*', (new UserAlert)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'user_alert_show';
                $editGate      = 'user_alert_edit';
                $deleteGate    = 'user_alert_delete';
                $crudRoutePart = 'user-alerts';

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
            $table->editColumn('alert_text', function ($row) {
                return $row->alert_text ? $row->alert_text : '';
            });
            $table->editColumn('alert_link', function ($row) {
                return $row->alert_link ? $row->alert_link : '';
            });
            $table->editColumn('user', function ($row) {
                $labels = [];
                foreach ($row->users as $user) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $user->name);
                }

                return implode(' ', $labels);
            });

            $table->rawColumns(['actions', 'placeholder', 'user']);

            return $table->make(true);
        }

        return view('admin.userAlerts.index');
    }

    public function create()
    {
        abort_if(Gate::denies('user_alert_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id');

        return view('admin.userAlerts.create', compact('users'));
    }

    public function store(StoreUserAlertRequest $request)
    {
        $userAlert = UserAlert::create($request->all());
        $userAlert->users()->sync($request->input('users', []));

        return redirect()->route('admin.user-alerts.index');
    }

    public function show(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->load('users');

        return view('admin.userAlerts.show', compact('userAlert'));
    }

    public function destroy(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserAlertRequest $request)
    {
        $userAlerts = UserAlert::find(request('ids'));

        foreach ($userAlerts as $userAlert) {
            $userAlert->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function read(Request $request)
    {
        $alerts = \Auth::user()->userUserAlerts()->where('read', false)->get();
        foreach ($alerts as $alert) {
            $pivot       = $alert->pivot;
            $pivot->read = true;
            $pivot->save();
        }
    }


   public function fetch()
{
    if (!auth()->check()) {
        return response()->json(['count' => 0, 'html' => '']);
    }

    $user = auth()->user();
    $alerts = $user->userUserAlerts()->withPivot('read')->latest()->limit(10)->get();

    $html = '';
    if ($alerts->count()) {
        foreach ($alerts as $alert) {
            $html .= '<div class="dropdown-item">';
            $html .= '<a href="' . ($alert->alert_link ?? '#') . '" target="_blank" rel="noopener noreferrer">';
            if ($alert->pivot->read === 0) {
                $html .= '<strong>';
            }
            $html .= e($alert->alert_text);
            if ($alert->pivot->read === 0) {
                $html .= '</strong>';
            }
            $html .= '</a></div>';
        }
    } else {
        $html = '<div class="text-center">No alerts</div>';
    }

    return response()->json([
        'count' => $user->userUserAlerts()->where('read', false)->count(),
        'html' => $html,
    ]);
}

    public function submit(Request $request)
{
    Log::info('Submit alert request received', $request->all());

    $request->validate([
        'alert_text' => 'required|string|max:255',
        'alert_link' => 'nullable|string',
        'user_ids' => 'required|array',
        'user_ids.*' => 'exists:users,id',
    ]);

    $adminId = auth()->id(); // or hardcoded admin ID like 1
    $userIds = $request->input('user_ids');

    // Add admin ID if not already present
    if (!in_array($adminId, $userIds)) {
        $userIds[] = $adminId;
    }

    $alert = UserAlert::create([
        'alert_text' => $request->input('alert_text'),
        'alert_link' => $request->input('alert_link'),
    ]);

    $alert->users()->sync($userIds);

    Log::info('Alert created with ID', ['id' => $alert->id]);

    return response()->json([
        'message' => 'Alert submitted successfully.',
        'alert_id' => $alert->id,
    ], 201);
}


} 