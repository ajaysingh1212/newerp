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

 public function show($id)
    {
        $recharge = KycRecharge::with('user', 'vehicle', 'createdBy')->findOrFail($id);
        return view('admin.kyc_recharge.show', compact('recharge'));
    }

    // Show create form
   public function create(Request $request)
    {
        $users = User::all();
        $vehicles = AddCustomerVehicle::all();

        $selectedVehicle = null;
        if ($request->has('vehicle_number')) {
            $selectedVehicle = AddCustomerVehicle::where('vehicle_number', $request->vehicle_number)->first();
        }

        return view('admin.kyc-recharge.create', compact('users', 'vehicles', 'selectedVehicle'));
    }

    // Store new recharge
    public function store(Request $request)
{
    $data = $request->validate([
        'vehicle_number' => 'required|string|max:50',
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'payment_amount' => 'required|numeric',
        'payment_status' => 'required|in:pending,completed,failed',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'location' => 'nullable|string|max:255',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $data['user_id'] = Auth::id();
    $data['created_by_id'] = Auth::id();
    $data['payment_status'] = 'pending';

    // ✅ Image upload
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('kyc-recharge-images', 'public');
        $data['image'] = $path;
    }

    // Create KYC Recharge entry
    $recharge = KycRecharge::create($data);

    try {
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
    } catch (\Exception $e) {
        $recharge->delete();
        return response()->json([
            'error' => 'Error creating Razorpay order: ' . $e->getMessage()
        ], 500);
    }
}


    // Payment callback
  // JSON callback endpoint
public function paymentCallbackJson(Request $request, $id)
{
    $recharge = KycRecharge::findOrFail($id);
    $recharge->payment_status = 'completed';
    $recharge->payment_method = 'Razorpay';
    $recharge->payment_date = now();
    $recharge->save();

    return response()->json([
        'success' => true,
        'redirect' => route('admin.kyc-recharges.index')
    ]);
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
