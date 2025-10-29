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

        $data = $request->validate([
            'vehicle_number'  => 'required|string|max:50',
            'vehicle_id'      => 'required|exists:add_customer_vehicles,id',
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'payment_amount'  => 'required|numeric|min:1',
            'image'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_base64'    => 'nullable|string',
            'location'        => 'nullable|string|max:255',
            'latitude'        => 'nullable|numeric',
            'longitude'       => 'nullable|numeric',
        ]);

        $data['user_id']        = $user->id;
        $data['created_by_id']  = $user->id;
        $data['payment_status'] = $user->is_admin ? 'completed' : 'pending';

        $recharge = KycRecharge::create($data);

        // ✅ Image upload
        if ($request->hasFile('image')) {
            $recharge->addMediaFromRequest('image')->toMediaCollection('kyc_recharge_images');
        } elseif ($request->filled('image_base64')) {
            $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $request->image_base64);
            $tempPath = storage_path('app/tmp_' . uniqid() . '.png');
            file_put_contents($tempPath, base64_decode($base64Image));
            $recharge->addMedia($tempPath)->usingFileName('camera_' . time() . '.png')->toMediaCollection('kyc_recharge_images');
            @unlink($tempPath);
        }

        // ✅ Non-admin: create Razorpay order
        if (!$user->is_admin) {
            $keyId     = env('RAZORPAY_KEY_ID');
            $keySecret = env('RAZORPAY_KEY_SECRET');

            if (!$keyId || !$keySecret) {
                throw new \Exception('Razorpay credentials missing.');
            }

            $api = new Api($keyId, $keySecret);
            $amount = intval($data['payment_amount'] * 100);

            if ($amount <= 0) throw new \Exception('Invalid payment amount.');

            $order = $api->order->create([
                'receipt'         => 'rcpt_' . $recharge->id,
                'amount'          => $amount,
                'currency'        => 'INR',
                'payment_capture' => 1,
            ]);

            if (!isset($order['id'])) {
                throw new \Exception('Razorpay order creation failed.');
            }

            $recharge->update(['razorpay_order_id' => $order['id']]);

            return response()->json([
                'success'           => true,
                'id'                => $recharge->id,
                'payment_amount'    => $recharge->payment_amount,
                'razorpay_order_id' => $order['id'],
                'image_url'         => $recharge->getFirstMediaUrl('kyc_recharge_images'),
            ]);
        }

        // ✅ Admin direct success
        return response()->json([
            'success'        => true,
            'id'             => $recharge->id,
            'payment_amount' => $recharge->payment_amount,
            'payment_status' => 'completed',
            'image_url'      => $recharge->getFirstMediaUrl('kyc_recharge_images'),
            'redirect'       => route('admin.kyc-recharges.index'),
        ]);

    } catch (\Illuminate\Validation\ValidationException $ve) {
        return response()->json([
            'error'   => 'Validation failed',
            'details' => $ve->errors(),
        ], 422);
    } catch (\Exception $e) {
        Log::error('❌ KYC Recharge Store Error: ' . $e->getMessage());
        return response()->json([
            'error'   => 'Something went wrong while creating recharge.',
            'message' => $e->getMessage(),
        ], 500);
    }
}


  public function paymentCallbackJson(Request $request, $id)
{
    try {
        $user = auth()->user();
        $recharge = KycRecharge::findOrFail($id);

        // ✅ Razorpay keys
        $keyId     = env('RAZORPAY_KEY_ID');
        $keySecret = env('RAZORPAY_KEY_SECRET');

        if (!$keyId || !$keySecret) {
            throw new \Exception('Razorpay credentials missing.');
        }

        // ✅ Request validation
        $data = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        // ✅ Initialize API
        $api = new Api($keyId, $keySecret);

        // ✅ Signature verification
        $attributes = [
            'razorpay_order_id'   => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
        ];

        $api->utility->verifyPaymentSignature($attributes);

        // ✅ Fetch payment details
        $payment = $api->payment->fetch($data['razorpay_payment_id']);

        // ✅ Update recharge record
        $recharge->update([
            'payment_status'      => 'completed',
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'razorpay_signature'  => $data['razorpay_signature'],
            'paid_at'             => now(),
        ]);

        Log::info('✅ Razorpay payment success', [
            'recharge_id' => $recharge->id,
            'payment_id'  => $data['razorpay_payment_id'],
            'user_id'     => $user->id,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Payment verified successfully.',
            'redirect' => route('admin.kyc-recharges.index'),
        ]);

    } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
        Log::error('❌ Razorpay signature mismatch: ' . $e->getMessage());
        $recharge = KycRecharge::find($id);
        if ($recharge) {
            $recharge->update(['payment_status' => 'failed']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Signature verification failed. Payment not valid.',
        ], 400);

    } catch (\Illuminate\Validation\ValidationException $ve) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid payment data.',
            'errors'  => $ve->errors(),
        ], 422);

    } catch (\Exception $e) {
        Log::error('❌ Razorpay Callback Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);

        $recharge = KycRecharge::find($id);
        if ($recharge) {
            $recharge->update(['payment_status' => 'failed']);
        }

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong during payment verification.',
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