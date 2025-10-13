<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycRecharge;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AddCustomerVehicle;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
class KycRechargeController extends Controller
{
    // List all KYC Recharges
   public function index()
{
    $recharges = KycRecharge::with('user', 'vehicle', 'createdBy')->get();
    
    return view('admin.kyc-recharge.index', compact('recharges')); // dash (-) correctly used
}


    // Show create form
   public function create(Request $request)
{
    $users = User::all();
    $vehicles = AddCustomerVehicle::all();

    // get vehicle number from query if available
    $selectedVehicle = null;
    if ($request->has('vehicle_number')) {
        $selectedVehicle = AddCustomerVehicle::where('vehicle_number', $request->vehicle_number)->first();
    }

    return view('admin.kyc-recharge.create', compact('users', 'vehicles', 'selectedVehicle'));
}


    // Show single recharge
    public function show($id)
    {
        $recharge = KycRecharge::with('user', 'vehicle', 'createdBy')->findOrFail($id);
        return view('admin.kyc_recharge.show', compact('recharge'));
    }

    // Store new recharge
 public function store(Request $request)
{
    $data = $request->validate([
        'vehicle_number' => 'nullable|string|max:50',
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'payment_amount' => 'required|numeric',
        'payment_status' => 'required|in:pending,completed,failed',
    ]);

    $data['user_id'] = Auth::id();
    $data['created_by_id'] = Auth::id();
    $data['payment_status'] = 'pending';

    $recharge = KycRecharge::create($data);

    $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
    $order = $api->order->create([
        'receipt' => 'rcpt_' . $recharge->id,
        'amount' => $data['payment_amount'] * 100,
        'currency' => 'INR',
        'payment_capture' => 1
    ]);

    $recharge->razorpay_order_id = $order['id'];
    $recharge->save();

    return response()->json([
        'id' => $recharge->id,
        'payment_amount' => $recharge->payment_amount,
        'razorpay_order_id' => $order['id']
    ]);
}


public function paymentCallback(Request $request, $id)
{
    $recharge = KycRecharge::findOrFail($id);

    // You can verify Razorpay payment signature here

    $recharge->payment_status = 'completed';
    $recharge->payment_method = 'Razorpay';
    $recharge->payment_date = now();
    $recharge->save();

    return redirect()->route('admin.kyc-recharges.index')
                     ->with('success', 'Payment completed successfully.');
}


    // Show edit form
    public function edit($id)
    {
        $recharge = KycRecharge::findOrFail($id);
        $users = User::all();
        $vehicles = AddCustomerVehicle::all();

        return view('admin.kyc_recharge.edit', compact('recharge', 'users', 'vehicles'));
    }

    // Update recharge
    public function update(Request $request, $id)
    {
        $recharge = KycRecharge::findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'payment_status' => 'nullable|in:pending,completed,failed',
            'payment_method' => 'nullable|string|max:100',
            'payment_amount' => 'nullable|numeric',
            'payment_date' => 'nullable|date',
        ]);

        $recharge->update($data);

        return redirect()->route('admin.kyc-recharges.index')
                         ->with('success', 'KYC Recharge updated successfully.');
    }

    // Delete recharge
    public function destroy($id)
    {
        $recharge = KycRecharge::findOrFail($id);
        $recharge->delete();

        return redirect()->route('admin.kyc-recharges.index')
                         ->with('success', 'KYC Recharge deleted successfully.');
    }
}
