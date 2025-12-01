<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyAgentRequest;
use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class AgentsController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('agent_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $agents = Agent::with(['created_by', 'media'])->get();

        return view('admin.agents.index', compact('agents'));
    }

    public function create()
    {
        abort_if(Gate::denies('agent_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.agents.create');
    }

    public function store(StoreAgentRequest $request)
    {
        $agent = Agent::create($request->all());

        if ($request->input('aadhar_front', false)) {
            $agent->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhar_front'))))->toMediaCollection('aadhar_front');
        }

        if ($request->input('aadhar_back', false)) {
            $agent->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhar_back'))))->toMediaCollection('aadhar_back');
        }

        if ($request->input('pan_card', false)) {
            $agent->addMedia(storage_path('tmp/uploads/' . basename($request->input('pan_card'))))->toMediaCollection('pan_card');
        }

        foreach ($request->input('additional_document', []) as $file) {
            $agent->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('additional_document');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $agent->id]);
        }

        return redirect()->route('admin.agents.index');
    }

    public function edit(Agent $agent)
    {
        abort_if(Gate::denies('agent_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $agent->load('created_by');

        return view('admin.agents.edit', compact('agent'));
    }

    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        $agent->update($request->all());

        if ($request->input('aadhar_front', false)) {
            if (! $agent->aadhar_front || $request->input('aadhar_front') !== $agent->aadhar_front->file_name) {
                if ($agent->aadhar_front) {
                    $agent->aadhar_front->delete();
                }
                $agent->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhar_front'))))->toMediaCollection('aadhar_front');
            }
        } elseif ($agent->aadhar_front) {
            $agent->aadhar_front->delete();
        }

        if ($request->input('aadhar_back', false)) {
            if (! $agent->aadhar_back || $request->input('aadhar_back') !== $agent->aadhar_back->file_name) {
                if ($agent->aadhar_back) {
                    $agent->aadhar_back->delete();
                }
                $agent->addMedia(storage_path('tmp/uploads/' . basename($request->input('aadhar_back'))))->toMediaCollection('aadhar_back');
            }
        } elseif ($agent->aadhar_back) {
            $agent->aadhar_back->delete();
        }

        if ($request->input('pan_card', false)) {
            if (! $agent->pan_card || $request->input('pan_card') !== $agent->pan_card->file_name) {
                if ($agent->pan_card) {
                    $agent->pan_card->delete();
                }
                $agent->addMedia(storage_path('tmp/uploads/' . basename($request->input('pan_card'))))->toMediaCollection('pan_card');
            }
        } elseif ($agent->pan_card) {
            $agent->pan_card->delete();
        }

        if (count($agent->additional_document) > 0) {
            foreach ($agent->additional_document as $media) {
                if (! in_array($media->file_name, $request->input('additional_document', []))) {
                    $media->delete();
                }
            }
        }
        $media = $agent->additional_document->pluck('file_name')->toArray();
        foreach ($request->input('additional_document', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $agent->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('additional_document');
            }
        }

        return redirect()->route('admin.agents.index');
    }

    public function show(Agent $agent)
    {
        abort_if(Gate::denies('agent_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $agent->load('created_by', 'selectAgentInvestments');

        return view('admin.agents.show', compact('agent'));
    }

    public function destroy(Agent $agent)
    {
        abort_if(Gate::denies('agent_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $agent->delete();

        return back();
    }

    public function massDestroy(MassDestroyAgentRequest $request)
    {
        $agents = Agent::find(request('ids'));

        foreach ($agents as $agent) {
            $agent->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('agent_create') && Gate::denies('agent_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Agent();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}