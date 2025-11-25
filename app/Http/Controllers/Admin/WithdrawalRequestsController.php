<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyWithdrawalRequestRequest;
use App\Http\Requests\StoreWithdrawalRequestRequest;
use App\Http\Requests\UpdateWithdrawalRequestRequest;
use App\Models\Investment;
use App\Models\Registration;
use App\Models\WithdrawalRequest;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalRequestsController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('withdrawal_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequests = WithdrawalRequest::with(['select_investor', 'investment', 'created_by'])->get();

        return view('admin.withdrawalRequests.index', compact('withdrawalRequests'));
    }

    public function create()
    {
        abort_if(Gate::denies('withdrawal_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investments = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.withdrawalRequests.create', compact('investments', 'select_investors'));
    }

    public function store(StoreWithdrawalRequestRequest $request)
    {
        $withdrawalRequest = WithdrawalRequest::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $withdrawalRequest->id]);
        }

        return redirect()->route('admin.withdrawal-requests.index');
    }

    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investments = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $withdrawalRequest->load('select_investor', 'investment', 'created_by');

        return view('admin.withdrawalRequests.edit', compact('investments', 'select_investors', 'withdrawalRequest'));
    }

    public function update(UpdateWithdrawalRequestRequest $request, WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->update($request->all());

        return redirect()->route('admin.withdrawal-requests.index');
    }

    public function show(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequest->load('select_investor', 'investment', 'created_by');

        return view('admin.withdrawalRequests.show', compact('withdrawalRequest'));
    }

    public function destroy(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequest->delete();

        return back();
    }

    public function massDestroy(MassDestroyWithdrawalRequestRequest $request)
    {
        $withdrawalRequests = WithdrawalRequest::find(request('ids'));

        foreach ($withdrawalRequests as $withdrawalRequest) {
            $withdrawalRequest->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('withdrawal_request_create') && Gate::denies('withdrawal_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new WithdrawalRequest();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
    public function storeAjax(Request $request)
{
    $request->validate([
        'investment_id' => 'required',
        'amount'        => 'required|numeric|min:1',
        'type'          => 'required',
    ]);

    $investment = Investment::findOrFail($request->investment_id);

    WithdrawalRequest::create([
        'select_investor_id' => $investment->select_investor_id,
        'investment_id'      => $investment->id,
        'amount'             => $request->amount,
        'type'               => $request->type,
        'status'             => 'pending',
        'requested_at'       => now(),
        'notes'              => $request->notes,
        'created_by_id'      => auth()->id()
    ]);

    return response()->json(['message' => 'Withdrawal request submitted successfully.']);
}

}
