<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KycRecharge;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AddCustomerVehicle;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
 

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

 
 public function store(Request $request)
    {
        try {
            // ✅ Ensure payment_status is set
            $request->merge(['payment_status' => 'pending']);

            // ✅ Validate input
            $data = $request->validate([
                'vehicle_number' => 'required|string|max:50',
                'vehicle_id' => 'required|exists:add_customer_vehicles,id',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'payment_amount' => 'required|numeric',
                'payment_status' => 'required|in:pending,completed,failed',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'location' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'image_base64' => 'nullable|string',
            ]);

            $data['user_id'] = Auth::id();
            $data['created_by_id'] = Auth::id();

            // ✅ Handle uploaded image
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('kyc-recharge-images', 'public');
            }
            

            // ✅ Handle base64 camera image
            if ($request->filled('image_base64')) {
                $imageData = str_replace('data:image/png;base64,', '', $request->image_base64);
                $imageData = str_replace(' ', '+', $imageData);
                $imageName = 'camera_' . time() . '.png';
                \File::put(public_path('storage/kyc-recharge-images/' . $imageName), base64_decode($imageData));
                $data['image'] = 'kyc-recharge-images/' . $imageName;
            }

            // ✅ Create recharge record
            $recharge = KycRecharge::create($data);

            // ✅ Create Razorpay order in separate try/catch
            try {
                $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
                $order = $api->order->create([
                    'receipt' => 'rcpt_' . $recharge->id,
                    'amount' => intval($data['payment_amount'] * 100), // in paise
                    'currency' => 'INR',
                    'payment_capture' => 1,
                ]);

                $recharge->razorpay_order_id = $order['id'];
                $recharge->save();
            } catch (\Exception $razorpayEx) {
                Log::error('Razorpay Order Error: ' . $razorpayEx->getMessage(), ['trace' => $razorpayEx->getTraceAsString()]);
                return response()->json([
                    'error' => 'Razorpay order creation failed. Check logs for details.'
                ], 500);
            }

            // ✅ Return JSON response
            return response()->json([
                'id' => $recharge->id,
                'payment_amount' => $recharge->payment_amount,
                'razorpay_order_id' => $order['id'],
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            // ✅ Return validation errors as JSON
            return response()->json([
                'error' => $ve->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('KYC Recharge Store Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Something went wrong while creating payment. Check server logs.'
            ], 500);
        }
    }

    public function paymentCallbackJson(Request $request, $id)
    {
        try {
            $recharge = KycRecharge::findOrFail($id);
            $recharge->payment_status = 'completed';
            $recharge->payment_method = 'Razorpay';
            $recharge->payment_date = now();
            $recharge->save();

            return response()->json([
                'success' => true,
                'redirect' => route('admin.kyc-recharges.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Callback Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Payment callback failed. Check logs.'
            ], 500);
        }
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
