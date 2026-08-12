<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RechargePlan;
use App\Models\RedeemCode;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedeemCodeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('redeem_code_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $redeemCodes = RedeemCode::with(['rechargePlan', 'usedBy.roles', 'createdBy.roles', 'rechargeRequest'])
            ->latest()
            ->get();

        return view('admin.redeemCodes.index', compact('redeemCodes'));
    }

    public function create()
    {
        abort_if(Gate::denies('redeem_code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rechargePlans = RechargePlan::orderBy('plan_name')
            ->get()
            ->mapWithKeys(function ($plan) {
                return [$plan->id => "{$plan->type} - {$plan->plan_name} - Rs {$plan->price}"];
            });

        $generatedCode = $this->generateUniqueCode();

        return view('admin.redeemCodes.create', compact('rechargePlans', 'generatedCode'));
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('redeem_code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $data = $request->validate([
            'recharge_plan_id' => 'required|exists:recharge_plans,id',
            'valid_up_to'     => 'required|date|after_or_equal:today',
            'discount_type'   => 'required|in:flat,percent',
            'discount_value'  => 'required|numeric|min:0.01',
            'code'            => ['required', 'regex:/^ET-\d{5}[A-Z]{5}$/', 'unique:redeem_codes,code'],
        ]);

        if ($data['discount_type'] === 'percent' && $data['discount_value'] > 100) {
            return back()->withInput()->withErrors(['discount_value' => 'Percent discount cannot be more than 100.']);
        }

        $plan = RechargePlan::findOrFail($data['recharge_plan_id']);
        $user = auth()->user();
        $creatorRole = $user->roles->pluck('title')->implode(', ');

        RedeemCode::create([
            'recharge_plan_id' => $plan->id,
            'code'             => strtoupper($data['code']),
            'valid_up_to'      => $data['valid_up_to'],
            'discount_type'    => $data['discount_type'],
            'discount_value'   => $data['discount_value'],
            'discount_amount'  => (new RedeemCode($data))->calculateDiscount((float) $plan->price),
            'status'           => 'active',
            'use_status'       => 'not_used',
            'created_by_id'    => $user->id,
            'creator_name'     => $user->name,
            'creator_role'     => $creatorRole ?: 'N/A',
        ]);

        return redirect()->route('admin.redeem-codes.index')->with('success', 'Redeem code created successfully.');
    }

    public function show(RedeemCode $redeemCode)
    {
        abort_if(Gate::denies('redeem_code_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $redeemCode->load(['rechargePlan', 'usedBy.roles', 'createdBy.roles', 'rechargeRequest']);

        return view('admin.redeemCodes.show', compact('redeemCode'));
    }

    public function destroy(RedeemCode $redeemCode)
    {
        abort_if(Gate::denies('redeem_code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($redeemCode->use_status === 'used') {
            return back()->with('error', 'Used redeem code cannot be deleted.');
        }

        $redeemCode->delete();

        return back()->with('success', 'Redeem code deleted successfully.');
    }

    public function validateCode(Request $request)
    {
        $data = $request->validate([
            'code'             => 'required|string',
            'recharge_plan_id' => 'required|exists:recharge_plans,id',
        ]);

        $plan = RechargePlan::findOrFail($data['recharge_plan_id']);
        $redeemCode = RedeemCode::where('code', strtoupper(trim($data['code'])))
            ->where('recharge_plan_id', $plan->id)
            ->first();

        if (! $redeemCode) {
            return response()->json(['valid' => false, 'message' => 'Invalid redeem code for selected plan.'], 422);
        }

        if ($redeemCode->status !== 'active' || $redeemCode->use_status !== 'not_used') {
            return response()->json(['valid' => false, 'message' => 'This redeem code is already used or inactive.'], 422);
        }

        if (\Carbon\Carbon::parse($redeemCode->valid_up_to)->lt(now()->startOfDay())) {
            return response()->json(['valid' => false, 'message' => 'This redeem code has expired.'], 422);
        }

        $discount = $redeemCode->calculateDiscount((float) $plan->price);

        return response()->json([
            'valid'          => true,
            'message'        => 'Redeem code applied successfully.',
            'discount_type'  => $redeemCode->discount_type,
            'discount_value' => (float) $redeemCode->discount_value,
            'discount'       => $discount,
        ]);
    }

    private function generateUniqueCode(): string
    {
        do {
            $letters = '';
            for ($i = 0; $i < 5; $i++) {
                $letters .= chr(random_int(65, 90));
            }

            $code = 'ET-' . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT) . $letters;
        } while (RedeemCode::where('code', $code)->exists());

        return $code;
    }
}
