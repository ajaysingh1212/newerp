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

    /* -------------------------------------------------------------
        SHOW LIST
    ------------------------------------------------------------- */
    public function index()
    {
        abort_if(Gate::denies('withdrawal_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequests = WithdrawalRequest::with(['select_investor', 'investment', 'created_by'])->latest()->get();

        return view('admin.withdrawalRequests.index', compact('withdrawalRequests'));
    }

    /* -------------------------------------------------------------
        MANUAL CREATE PAGE
    ------------------------------------------------------------- */
    public function create()
    {
        abort_if(Gate::denies('withdrawal_request_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');
        $investments = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.withdrawalRequests.create', compact('investments', 'select_investors'));
    }

    /* -------------------------------------------------------------
        STORE FROM ADMIN PANEL
    ------------------------------------------------------------- */
    public function store(StoreWithdrawalRequestRequest $request)
    {
        $investment = Investment::findOrFail($request->investment_id);

        // Processing hours
        $processing_hours = $request->type === 'interest' ? 48 : 336;

        $withdrawalRequest = WithdrawalRequest::create([
            'select_investor_id' => $investment->select_investor_id,
            'investment_id'      => $investment->id,
            'amount'             => $request->amount,
            'type'               => $request->type,
            'status'             => 'pending',
            'processing_hours'   => $processing_hours,
            'requested_at'       => now(),
            'approved_at'        => null,
            'notes'              => $request->notes,
            'remarks'            => null,
            'created_by_id'      => auth()->id(),
        ]);

        // Handle CKEditor media
        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $withdrawalRequest->id]);
        }

        return redirect()->route('admin.withdrawal-requests.index')
            ->with('success', 'Withdrawal Request Created Successfully.');
    }

    /* -------------------------------------------------------------
        EDIT FORM
    ------------------------------------------------------------- */
    public function edit(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $select_investors = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');
        $investments = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $withdrawalRequest->load('select_investor', 'investment', 'created_by');

        return view('admin.withdrawalRequests.edit', compact('investments', 'select_investors', 'withdrawalRequest'));
    }

    /* -------------------------------------------------------------
        UPDATE
    ------------------------------------------------------------- */
    public function update(UpdateWithdrawalRequestRequest $request, WithdrawalRequest $withdrawalRequest)
    {
        $withdrawalRequest->update($request->all());

        return redirect()->route('admin.withdrawal-requests.index')
            ->with('success', 'Withdrawal Request Updated Successfully.');
    }


    /* -------------------------------------------------------------
        SHOW PAGE
    ------------------------------------------------------------- */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequest->load('select_investor', 'investment', 'created_by');

        return view('admin.withdrawalRequests.show', compact('withdrawalRequest'));
    }

    /* -------------------------------------------------------------
        DELETE SINGLE
    ------------------------------------------------------------- */
    public function destroy(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequest->delete();

        return back();
    }

    /* -------------------------------------------------------------
        MASS DELETE
    ------------------------------------------------------------- */
    public function massDestroy(MassDestroyWithdrawalRequestRequest $request)
    {
        $withdrawalRequests = WithdrawalRequest::find(request('ids'));

        foreach ($withdrawalRequests as $withdrawalRequest) {
            $withdrawalRequest->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /* -------------------------------------------------------------
        CKEDITOR MEDIA UPLOAD
    ------------------------------------------------------------- */
    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('withdrawal_request_create') && Gate::denies('withdrawal_request_edit'), Response::HTTP_FORBIDDEN);

        $model         = new WithdrawalRequest();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;

        $media = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    /* -------------------------------------------------------------
         AJAX STORE FOR FRONTEND (USED IN INVESTMENTS DETAILS PAGE)
    ------------------------------------------------------------- */
    public function storeAjax(Request $request)
    {
        $request->validate([
            'investment_id' => 'required|integer',
            'amount'        => 'required|numeric|min:1',
            'type'          => 'required|string',
            'notes'         => 'nullable|string',
        ]);

        $investment = Investment::findOrFail($request->investment_id);

        // Processing hours logic
        $processing_hours = $request->type === 'interest' ? 48 : 336;

        WithdrawalRequest::create([
            'select_investor_id' => $investment->select_investor_id,
            'investment_id'      => $investment->id,
            'amount'             => $request->amount,
            'type'               => $request->type,
            'status'             => 'pending',
            'processing_hours'   => $processing_hours,
            'requested_at'       => now(),
            'approved_at'        => null,
            'notes'              => $request->notes,
            'remarks'            => null,
            'created_by_id'      => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Withdrawal request submitted successfully.'
        ]);
    }
}
