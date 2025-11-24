<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyInvestorTransactionRequest;
use App\Http\Requests\StoreInvestorTransactionRequest;
use App\Http\Requests\UpdateInvestorTransactionRequest;
use App\Models\Investment;
use App\Models\InvestorTransaction;
use App\Models\Registration;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class InvestorTransactionController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('investor_transaction_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investorTransactions = InvestorTransaction::with(['investor', 'investment', 'created_by'])->get();

        return view('admin.investorTransactions.index', compact('investorTransactions'));
    }

    public function create()
    {
        abort_if(Gate::denies('investor_transaction_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investors = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investments = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.investorTransactions.create', compact('investments', 'investors'));
    }

    public function store(StoreInvestorTransactionRequest $request)
    {
        $investorTransaction = InvestorTransaction::create($request->all());

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $investorTransaction->id]);
        }

        return redirect()->route('admin.investor-transactions.index');
    }

    public function edit(InvestorTransaction $investorTransaction)
    {
        abort_if(Gate::denies('investor_transaction_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investors = Investment::pluck('principal_amount', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investments = Registration::pluck('reg', 'id')->prepend(trans('global.pleaseSelect'), '');

        $investorTransaction->load('investor', 'investment', 'created_by');

        return view('admin.investorTransactions.edit', compact('investments', 'investorTransaction', 'investors'));
    }

    public function update(UpdateInvestorTransactionRequest $request, InvestorTransaction $investorTransaction)
    {
        $investorTransaction->update($request->all());

        return redirect()->route('admin.investor-transactions.index');
    }

    public function show(InvestorTransaction $investorTransaction)
    {
        abort_if(Gate::denies('investor_transaction_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investorTransaction->load('investor', 'investment', 'created_by');

        return view('admin.investorTransactions.show', compact('investorTransaction'));
    }

    public function destroy(InvestorTransaction $investorTransaction)
    {
        abort_if(Gate::denies('investor_transaction_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $investorTransaction->delete();

        return back();
    }

    public function massDestroy(MassDestroyInvestorTransactionRequest $request)
    {
        $investorTransactions = InvestorTransaction::find(request('ids'));

        foreach ($investorTransactions as $investorTransaction) {
            $investorTransaction->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('investor_transaction_create') && Gate::denies('investor_transaction_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new InvestorTransaction();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
