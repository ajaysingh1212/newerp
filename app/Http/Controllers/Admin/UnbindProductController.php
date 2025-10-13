<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUnbindProductRequest;
use App\Http\Requests\StoreUnbindProductRequest;
use App\Http\Requests\UpdateUnbindProductRequest;
use Gate;
use App\Models\ProductMaster;
use App\Models\UnbindProduct;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnbindProductController extends Controller
{
public function index()
{
$products = ProductMaster::whereIn('id', function ($query) {
        $query->select('product_id')
            ->from('current_stocks')
            ->whereNull('transfer_user_id');
    })
    ->whereHas('imei', function ($query) {
        $query->whereRaw("LOWER(TRIM(product_status)) = ?", ['used']);
    })
    ->with(['imei', 'vts'])
    ->get();


    return view('admin.unbindProducts.index', compact('products'));
}

public function getProductDetails(Request $request)
{
    $product = ProductMaster::with(['imei', 'vts', 'product_model'])
        ->find($request->product_id);

    return response()->json($product);
}
public function unbind(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:product_masters,id',
    ]);

    $product = ProductMaster::findOrFail($request->product_id);

    // IMEI और VTS को update करें
    if ($product->imei) {
        $product->imei->update(['product_status' => 'Not Used']);
    }

    if ($product->vts) {
        $product->vts->update(['product_status' => 'Not Formed']);
    }

    // Current Stocks से भी product_id के रिकॉर्ड डिलीट करें
    \DB::table('current_stocks')->where('product_id', $product->id)->delete();

    // ProductMaster से soft delete करें
    $product->delete();

    return response()->json(['message' => 'Product unbound and deleted from current stocks successfully']);
}


   
}
