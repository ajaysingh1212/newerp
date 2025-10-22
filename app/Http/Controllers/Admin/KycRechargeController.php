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
  public function index(Request $request)
{
    // Get optional status from query parameter
    $status = $request->query('status'); // e.g., Pending, Completed, Failed, Total

    $recharges = KycRecharge::with('user', 'vehicle', 'createdBy')
        ->when($status && strtolower($status) !== 'total', function ($query) use ($status) {
            $query->where('payment_status', strtolower($status));
        })
        ->latest()
        ->get();

    return view('admin.kyc-recharge.index', compact('recharges', 'status'));
}


 public function show($id)
{
    $recharge = KycRecharge::with(['user', 'vehicle', 'createdBy'])->findOrFail($id);
    return view('admin.kyc-recharge.show', compact('recharge'));
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
            // ✅ Default payment status
            $request->merge(['payment_status' => 'pending']);

            // ✅ Validation
            $data = $request->validate([
                'vehicle_number' => 'required|string|max:50',
                'vehicle_id' => 'required|exists:add_customer_vehicles,id',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'payment_amount' => 'required|numeric',
                'payment_status' => 'required|in:pending,completed,failed',
                'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'image_base64' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            $data['user_id'] = Auth::id();
            $data['created_by_id'] = Auth::id();

            // ✅ Create recharge record (without image)
            $recharge = KycRecharge::create($data);

            // ✅ Handle uploaded file
            if ($request->hasFile('image')) {
                $recharge
                    ->addMediaFromRequest('image')
                    ->toMediaCollection('kyc_recharge_images');
            }

            // ✅ Handle Base64 image (from camera)
            if ($request->filled('image_base64')) {
                $imageData = $request->image_base64;

                // Extract base64 cleanly
                if (str_contains($imageData, 'base64,')) {
                    $imageData = explode('base64,', $imageData)[1];
                }

                $tempPath = storage_path('app/tmp_camera_' . time() . '.png');
                file_put_contents($tempPath, base64_decode($imageData));

                $recharge
                    ->addMedia($tempPath)
                    ->usingFileName('camera_' . time() . '.png')
                    ->toMediaCollection('kyc_recharge_images');

                @unlink($tempPath); // cleanup
            }

            // ✅ Create Razorpay order
            try {
                $api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

                $order = $api->order->create([
                    'receipt' => 'rcpt_' . $recharge->id,
                    'amount' => intval($data['payment_amount'] * 100),
                    'currency' => 'INR',
                    'payment_capture' => 1,
                ]);

                $recharge->razorpay_order_id = $order['id'];
                $recharge->save();
            } catch (\Exception $razorpayEx) {
                Log::error('Razorpay Order Error: ' . $razorpayEx->getMessage(), [
                    'trace' => $razorpayEx->getTraceAsString()
                ]);

                return response()->json([
                    'error' => 'Razorpay order creation failed. Check logs for details.'
                ], 500);
            }

            // ✅ Return JSON Response
            return response()->json([
                'id' => $recharge->id,
                'payment_amount' => $recharge->payment_amount,
                'razorpay_order_id' => $order['id'],
                'image_url' => $recharge->getFirstMediaUrl('kyc_recharge_images'),
            ]);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json(['error' => $ve->errors()], 422);
        } catch (\Exception $e) {
            Log::error('KYC Recharge Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Something went wrong while creating payment. Check server logs.'
            ], 500);
        }
    }

    public function paymentCallbackJson(Request $request, $id)
    {
        try {
            $recharge = KycRecharge::findOrFail($id);
            $recharge->update([
                'payment_status' => 'completed',
                'payment_method' => 'Razorpay',
                'payment_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.kyc-recharges.index')
            ]);
        } catch (\Exception $e) {
            Log::error('Payment Callback Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

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
