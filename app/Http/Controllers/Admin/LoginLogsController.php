<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyLoginLogRequest;
use App\Http\Requests\StoreLoginLogRequest;
use App\Http\Requests\UpdateLoginLogRequest;
use App\Models\LoginLog;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginLogsController extends Controller
{
    use CsvImportTrait;

    /* -------------------------------------------------------------
        INDEX (ROLE BASED LISTING)
    ------------------------------------------------------------- */
    public function index()
    {
        abort_if(Gate::denies('login_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $isAdmin = $user->roles->contains('title', 'Admin');

        if ($isAdmin) {
            // Admin sees all logs
            $loginLogs = LoginLog::with(['use', 'created_by'])
                ->latest()
                ->get();
        } else {
            // Normal user sees only his own logs
            $loginLogs = LoginLog::with(['use', 'created_by'])
                ->where('created_by_id', $user->id)
                ->latest()
                ->get();
        }

        return view('admin.loginLogs.index', compact('loginLogs'));
    }

    /* -------------------------------------------------------------
        CREATE PAGE
    ------------------------------------------------------------- */
    public function create()
    {
        abort_if(Gate::denies('login_log_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uses = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.loginLogs.create', compact('uses'));
    }

    /* -------------------------------------------------------------
        STORE (AUTO SET created_by_id)
    ------------------------------------------------------------- */
    public function store(StoreLoginLogRequest $request)
    {
        $data = $request->all();
        $data['created_by_id'] = auth()->id(); // Auto set logged-in user ID

        LoginLog::create($data);

        return redirect()->route('admin.login-logs.index');
    }

    /* -------------------------------------------------------------
        EDIT
    ------------------------------------------------------------- */
    public function edit(LoginLog $loginLog)
    {
        abort_if(Gate::denies('login_log_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uses = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $loginLog->load('use', 'created_by');

        return view('admin.loginLogs.edit', compact('loginLog', 'uses'));
    }

    /* -------------------------------------------------------------
        UPDATE
    ------------------------------------------------------------- */
    public function update(UpdateLoginLogRequest $request, LoginLog $loginLog)
    {
        $loginLog->update($request->all());

        return redirect()->route('admin.login-logs.index');
    }

    /* -------------------------------------------------------------
        SHOW
    ------------------------------------------------------------- */
    public function show(LoginLog $loginLog)
    {
        abort_if(Gate::denies('login_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loginLog->load('use', 'created_by');

        return view('admin.loginLogs.show', compact('loginLog'));
    }

    /* -------------------------------------------------------------
        DELETE
    ------------------------------------------------------------- */
    public function destroy(LoginLog $loginLog)
    {
        abort_if(Gate::denies('login_log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loginLog->delete();

        return back();
    }

    /* -------------------------------------------------------------
        MASS DELETE
    ------------------------------------------------------------- */
    public function massDestroy(MassDestroyLoginLogRequest $request)
    {
        $loginLogs = LoginLog::find(request('ids'));

        foreach ($loginLogs as $loginLog) {
            $loginLog->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
