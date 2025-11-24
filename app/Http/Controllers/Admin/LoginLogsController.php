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

    public function index()
    {
        abort_if(Gate::denies('login_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loginLogs = LoginLog::with(['use', 'created_by'])->get();

        return view('admin.loginLogs.index', compact('loginLogs'));
    }

    public function create()
    {
        abort_if(Gate::denies('login_log_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uses = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.loginLogs.create', compact('uses'));
    }

    public function store(StoreLoginLogRequest $request)
    {
        $loginLog = LoginLog::create($request->all());

        return redirect()->route('admin.login-logs.index');
    }

    public function edit(LoginLog $loginLog)
    {
        abort_if(Gate::denies('login_log_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $uses = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $loginLog->load('use', 'created_by');

        return view('admin.loginLogs.edit', compact('loginLog', 'uses'));
    }

    public function update(UpdateLoginLogRequest $request, LoginLog $loginLog)
    {
        $loginLog->update($request->all());

        return redirect()->route('admin.login-logs.index');
    }

    public function show(LoginLog $loginLog)
    {
        abort_if(Gate::denies('login_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loginLog->load('use', 'created_by');

        return view('admin.loginLogs.show', compact('loginLog'));
    }

    public function destroy(LoginLog $loginLog)
    {
        abort_if(Gate::denies('login_log_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $loginLog->delete();

        return back();
    }

    public function massDestroy(MassDestroyLoginLogRequest $request)
    {
        $loginLogs = LoginLog::find(request('ids'));

        foreach ($loginLogs as $loginLog) {
            $loginLog->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
