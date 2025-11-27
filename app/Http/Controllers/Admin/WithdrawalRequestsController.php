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
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalRequestsController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    /* -------------------------------------------------------------
        SHOW LIST — ROLE BASED
    ------------------------------------------------------------- */
    public function index()
    {
        abort_if(Gate::denies('withdrawal_request_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = auth()->user();
        $isAdmin = $user->roles->contains('title', 'Admin');

        if ($isAdmin) {
            // Admin → all data
            $withdrawalRequests = WithdrawalRequest::with(['select_investor', 'investment', 'created_by'])
                ->latest()
                ->get();
        } else {
            // Normal user → only own data
            $withdrawalRequests = WithdrawalRequest::with(['select_investor', 'investment', 'created_by'])
                ->where('created_by_id', $user->id)
                ->latest()
                ->get();
        }

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
        STORE (ADMIN PANEL) — ROLE BASED
    ------------------------------------------------------------- */
    public function store(StoreWithdrawalRequestRequest $request)
    {
        $investment = Investment::findOrFail($request->investment_id);

        $processing_hours = $request->type === 'interest' ? 48 : 336;

        $user = auth()->user();
        $isAdmin = $user->roles->contains('title', 'Admin');

        // created_by_id logic
        if ($isAdmin) {
            $createdBy = $investment->select_investor_id; // Admin → investor id
        } else {
            $createdBy = $user->id;                       // Normal user → own id
        }

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
            'created_by_id'      => $createdBy,
        ]);

        // CKEditor image handling
        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $withdrawalRequest->id]);
        }

        return redirect()->route('admin.withdrawal-requests.index')
            ->with('success', 'Withdrawal Request Created Successfully.');
    }

    /* -------------------------------------------------------------
        EDIT
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
        SHOW
    ------------------------------------------------------------- */
    public function show(WithdrawalRequest $withdrawalRequest)
    {
        abort_if(Gate::denies('withdrawal_request_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $withdrawalRequest->load('select_investor', 'investment', 'created_by');

        return view('admin.withdrawalRequests.show', compact('withdrawalRequest'));
    }

    /* -------------------------------------------------------------
        DELETE
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
        CKEDITOR IMAGE UPLOAD
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
        AJAX STORE (FRONTEND) — ROLE BASED
    ------------------------------------------------------------- */
    public function storeAjax(Request $request)
    {
        $request->validate([
            'investment_id' => 'required|integer',
            'amount'        => 'required|numeric|min:1',
            'type'          => 'required|string',
            'notes'         => 'nullable|string',
        ]);

        $investment = Investment::with('select_plan')->findOrFail($request->investment_id);

        $today = Carbon::today();
        $startDate = Carbon::parse($investment->start_date);

        $plan = $investment->select_plan;
        $lockinDays = $plan->lockin_days ? intval($plan->lockin_days) : 0;

        // Lock-in date validation
        if (!empty($investment->lockin_end_date)) {
            $endDate = Carbon::parse($investment->lockin_end_date);

            if ($today->lt($endDate)) {
                $diff = max(1, $today->diffInDays($endDate));

                return response()->json([
                    'status'  => false,
                    'message' => "You can withdraw after {$diff} days.",
                ], 422);
            }
        }

        $daysPassed = $startDate->diffInDays($today);

        if ($daysPassed < $lockinDays) {
            $remaining = max(1, $lockinDays - $daysPassed);

            return response()->json([
                'status'  => false,
                'message' => "You can withdraw after {$remaining} more days (plan lock).",
            ], 422);
        }

        $processing_hours = $request->type === 'interest' ? 48 : 336;

        $user = auth()->user();
        $isAdmin = $user->roles->contains('title', 'Admin');

        if ($isAdmin) {
            $createdBy = $investment->select_investor_id;
        } else {
            $createdBy = $user->id;
        }

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
            'created_by_id'      => $createdBy,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Withdrawal request submitted successfully.'
        ]);
    }

}
