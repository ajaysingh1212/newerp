<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualProduct;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManualProductController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('manual_product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $query = ManualProduct::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(15);

        return view('admin.manual-products.index', compact('products'));
    }

    public function create()
    {
        abort_if(Gate::denies('manual_product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-products.create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('manual_product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ManualProduct::create($validated);

        return redirect()->route('admin.manual-products.index')
            ->with('status', 'Product successfully created / Product ban gaya.');
    }

    public function edit(ManualProduct $manualProduct)
    {
        abort_if(Gate::denies('manual_product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-products.edit', compact('manualProduct'));
    }

    public function update(Request $request, ManualProduct $manualProduct)
    {
        abort_if(Gate::denies('manual_product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $manualProduct->update($validated);

        return redirect()->route('admin.manual-products.index')
            ->with('status', 'Product successfully updated / Product update ho gaya.');
    }

    public function show(ManualProduct $manualProduct)
    {
        abort_if(Gate::denies('manual_product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.manual-products.show', compact('manualProduct'));
    }

    public function destroy(ManualProduct $manualProduct)
    {
        abort_if(Gate::denies('manual_product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $manualProduct->delete();

        return back()->with('status', 'Product successfully deleted / Product delete ho gaya.');
    }

    public function massDestroy(Request $request)
    {
        abort_if(Gate::denies('manual_product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        ManualProduct::whereIn('id', $request->ids)->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
