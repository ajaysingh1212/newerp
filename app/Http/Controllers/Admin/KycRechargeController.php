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
use Gate;
use Symfony\Component\HttpFoundation\Response;
 

use Carbon\Carbon;

class KycRechargeController extends Controller
{
    // List all KYC Recharges
 public function index(Request $request)
{
    abort_if(Gate::denies('kyc_recharge_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    $user = auth()->user();

    // Read query parameters
    $paymentStatus = $request->query('payment_status'); 
    $filterType    = $request->query('filter_type');
    $fromDate      = $request->query('from_date');
    $toDate        = $request->query('to_date');

    // Apply filters
    $recharges = KycRecharge::with(['user', 'vehicle', 'createdBy'])
        ->when(!$user->is_admin, function ($query) use ($user) {
            $query->where('created_by_id', $user->id);
        })
        ->when($paymentStatus, function ($query) use ($paymentStatus) {
            $query->where('payment_status', strtolower($paymentStatus));
        })
        ->when($filterType, function ($query) use ($filterType, $fromDate, $toDate) {
            switch ($filterType) {
                case 'today':
                    $query->whereDate('payment_date', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('payment_date', Carbon::yesterday());
                    break;
                case '7_days':
                    $query->where('payment_date', '>=', Carbon::now()->subDays(7));
                    break;
                case '15_days':
                    $query->where('payment_date', '>=', Carbon::now()->subDays(15));
                    break;
                case '1_month':
                    $query->where('payment_date', '>=', Carbon::now()->subMonth());
                    break;
                case 'custom':
                    if ($fromDate && $toDate) {
                        $query->whereBetween('payment_date', [
                            Carbon::parse($fromDate)->startOfDay(),
                            Carbon::parse($toDate)->endOfDay(),
                        ]);
                    }
                    break;
            }
        })
        ->latest('payment_date')
        ->get();

    // Total amount (after filters)
    $totalAmount = $recharges->sum('payment_amount');

    return view('admin.kyc-recharge.index', compact(
        'recharges',
        'paymentStatus',
        'filterType',
        'fromDate',
        'toDate',
        'totalAmount'
    ));
}




 public function show($id)
{
    abort_if(Gate::denies('kyc_recharge_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $recharge = KycRecharge::with(['user', 'vehicle', 'createdBy'])->findOrFail($id);
    return view('admin.kyc-recharge.show', compact('recharge'));
}



    // Show create form
 public function create(Request $request)
{
    abort_if(Gate::denies('kyc_recharge_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    $user = auth()->user(); // current logged-in user

    // Fetch users (admins can assign, users cannot)
    $users = User::all();

    // Show all vehicles if admin, otherwise only vehicles created by this user
    if ($user->is_admin) {
        $vehicles = AddCustomerVehicle::all();
    } else {
        $vehicles = AddCustomerVehicle::where('created_by_id', $user->id)->get();
    }

    // Handle preselected vehicle (optional, e.g., from query param)
    $selectedVehicle = null;
    if ($request->has('vehicle_number')) {
        $selectedVehicle = AddCustomerVehicle::where('vehicle_number', $request->vehicle_number)->first();
    }

    return view('admin.kyc-recharge.create', compact('users', 'vehicles', 'selectedVehicle'));
}


 
 public function store(Request $request)
{
    try {
        $user = auth()->user();
        $vehicle = AddCustomerVehicle::find($request->vehicle_id);
        // ✅ Default payment status
        $paymentStatus = $user->is_admin ? 'completed' : 'pending';
        $request->merge(['payment_status' => $paymentStatus]);

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

        $data['user_id'] = $vehicle->id;
        $data['created_by_id'] = $user->id;

        // ✅ Create recharge record (without image)
        $recharge = KycRecharge::create($data);

        // ✅ Handle uploaded file
        if ($request->hasFile('image')) {
            $recharge->addMediaFromRequest('image')
                ->toMediaCollection('kyc_recharge_images');
        }

        // ✅ Handle Base64 image (from camera)
        if ($request->filled('image_base64')) {
            $imageData = $request->image_base64;

            if (str_contains($imageData, 'base64,')) {
                $imageData = explode('base64,', $imageData)[1];
            }

            $tempPath = storage_path('app/tmp_camera_' . time() . '.png');
            file_put_contents($tempPath, base64_decode($imageData));

            $recharge->addMedia($tempPath)
                ->usingFileName('camera_' . time() . '.png')
                ->toMediaCollection('kyc_recharge_images');

            @unlink($tempPath);
        }

        // ✅ Admin: directly mark as completed (no Razorpay)
        if (!$user->is_admin) {
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

                return response()->json([
                    'id' => $recharge->id,
                    'payment_amount' => $recharge->payment_amount,
                    'razorpay_order_id' => $order['id'],
                    'image_url' => $recharge->getFirstMediaUrl('kyc_recharge_images'),
                ]);
            } catch (\Exception $razorpayEx) {
                Log::error('Razorpay Order Error: ' . $razorpayEx->getMessage(), [
                    'trace' => $razorpayEx->getTraceAsString(),
                ]);

                return response()->json([
                    'error' => 'Razorpay order creation failed. Check logs for details.',
                ], 500);
            }
        }

        // ✅ Admin: return success directly
        return response()->json([
            'success' => true,
            'id' => $recharge->id,
            'payment_amount' => $recharge->payment_amount,
            'payment_status' => 'completed',
            'image_url' => $recharge->getFirstMediaUrl('kyc_recharge_images'),
        ]);

    } catch (\Illuminate\Validation\ValidationException $ve) {
        return response()->json(['error' => $ve->errors()], 422);

    } catch (\Exception $e) {
        Log::error('KYC Recharge Store Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => 'Something went wrong while creating payment. Check server logs.',
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
        abort_if(Gate::denies('kyc_recharge_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $recharge = KycRecharge::findOrFail($id);
        $users = User::all();
        $vehicles = AddCustomerVehicle::all();

        return view('admin.kyc-recharge.edit', compact('recharge', 'users', 'vehicles'));
    }

    // Update recharge
   public function update(Request $request, $id)
{
    $recharge = KycRecharge::findOrFail($id);

    // ✅ Sirf vehicle_status validate aur update karein
    $data = $request->validate([
        'vehicle_status' => 'required|in:processing,live',
    ]);

    $recharge->update([
        'vehicle_status' => $data['vehicle_status'],
    ]);

    return redirect()->route('admin.kyc-recharges.index')
                     ->with('success', 'Vehicle status updated successfully.');
}


    // Delete recharge
    public function destroy($id)
    {
        abort_if(Gate::denies('kyc_recharge_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $recharge = KycRecharge::findOrFail($id);
        $recharge->delete();

        return redirect()->route('admin.kyc-recharges.index')
                         ->with('success', 'KYC Recharge deleted successfully.');
    }
}