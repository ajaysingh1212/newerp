<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Models\CurrentStock;
use App\Models\ActivationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Models\CheckComplain;
use App\Models\AddCustomerVehicle;
use App\Models\ProductMaster;
use App\Models\StockTransfer;
use App\Models\KycRecharge;
use App\Models\RechargeRequest;
use App\Models\Investment;
use App\Models\Registration;
use App\Models\WithdrawalRequest;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch logged-in user's role
        $role = DB::table('role_user')
            ->where('user_id', $user->id)
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('roles.title')
            ->first();

        $userRole = $role->title ?? null;

        // If role is Customer, return minimal data
        if ($userRole === 'Customer') {
            return view('home', ['userRole' => $userRole]);
        }

        // ================================
        //  🔥 NEW: INVESTOR SUMMARY FILTERS
        // ================================
        $invFilter = $request->input('inv_filter', 'today');
        $invFrom = $request->input('inv_from');
        $invTo = $request->input('inv_to');

        switch ($invFilter) {
            case 'week':
                $invStart = Carbon::now()->startOfWeek();
                $invEnd   = Carbon::now()->endOfWeek();
                break;

            case 'month':
                $invStart = Carbon::now()->startOfMonth();
                $invEnd   = Carbon::now()->endOfMonth();
                break;

            case '3month':
                $invStart = Carbon::now()->subMonths(3);
                $invEnd   = Carbon::now();
                break;

            case '6month':
                $invStart = Carbon::now()->subMonths(6);
                $invEnd   = Carbon::now();
                break;

            case '9month':
                $invStart = Carbon::now()->subMonths(9);
                $invEnd   = Carbon::now();
                break;

            case '12month':
                $invStart = Carbon::now()->subMonths(12);
                $invEnd   = Carbon::now();
                break;

            default:
                $invStart = Carbon::now()->startOfDay();
                $invEnd   = Carbon::now()->endOfDay();
                break;
        }

        if ($invFrom && $invTo) {
            $invStart = Carbon::parse($invFrom);
            $invEnd = Carbon::parse($invTo)->endOfDay();
        }

        // ================================
        //  🔥 NEW: INVESTOR SUMMARY DATA
        // ================================
        $totalInvestors = Registration::count();

        $totalInvestmentAmount = Investment::whereBetween('created_at', [$invStart, $invEnd])
            ->sum('principal_amount');

        $totalPendingWithdraw = WithdrawalRequest::where('status', 'pending')
            ->whereBetween('requested_at', [$invStart, $invEnd])
            ->sum('amount');

        $totalApprovedWithdraw = WithdrawalRequest::where('status', 'approved')
            ->whereBetween('approved_at', [$invStart, $invEnd])
            ->sum('amount');

        // Pie chart data
        $investorChart = [
            'labels' => [
                'Total Investors',
                'Total Investment',
                'Approved Withdrawals',
                'Pending Withdrawals'
            ],
            'values' => [
                $totalInvestors,
                $totalInvestmentAmount,
                $totalApprovedWithdraw,
                $totalPendingWithdraw
            ]
        ];
        // ================================
        //  🔥 END INVESTOR SUMMARY BLOCK
        // ================================

        
        // ------------------ YOUR ORIGINAL CODE CONTINUES (NOT MODIFIED) ------------------
        $roles = Role::whereIn('title', ['CNF', 'Dealer', 'Distributer', 'Customer'])->pluck('id', 'title');
        $totals = $this->getUserTotals($user, $userRole, $roles);

        // Stock filters
        $timeFilter = $request->input('time_filter', 'today');
        $selectedRoleType = $request->input('role_type');
        $selectedUserId = $request->input('role_filter');

        $dateRange = match ($timeFilter) {
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
        };

        $stockQuery = CurrentStock::with('product')->whereBetween('created_at', $dateRange);

        $users = collect();

        if ($userRole === 'Admin') {
            if ($selectedRoleType) {
                $roleId = Role::where('title', $selectedRoleType)->value('id');
                $users = User::whereHas('roles', fn($q) => $q->where('id', $roleId))->pluck('name', 'id');
            }

            if ($selectedUserId) {
                $stockQuery->where('transfer_user_id', $selectedUserId);
            } else {
                $stockQuery->whereNull('transfer_user_id');
            }

        } else {
            $createdUsers = User::where('created_by_id', $user->id);

            if ($selectedRoleType) {
                $roleId = Role::where('title', $selectedRoleType)->value('id');
                $createdUsers->whereHas('roles', fn($q) => $q->where('id', $roleId));
            }

            $users = $createdUsers->pluck('name', 'id');

            if ($selectedUserId && $users->keys()->contains($selectedUserId)) {
                $stockQuery->where('transfer_user_id', $selectedUserId);
            } else {
                $stockQuery->where('transfer_user_id', $user->id);
            }
        }

        $stockData = $stockQuery->get();

                $chartLabels = $stockData->pluck('product.sku')->toArray();
        $chartValues = $stockData->groupBy('product.sku')->map(fn($items) =>
            $items->count()
        )->values();

        // Activation Filters
        $activationStatus = $request->input('activation_status');
        $activationFrom = $request->input('activation_from');
        $activationTo = $request->input('activation_to');
        $activationGranularity = $request->input('activation_group', 'day');

        $activationQuery = ActivationRequest::query();

        if ($activationStatus) {
            $activationQuery->where('status', $activationStatus);
        }

        if ($activationFrom && $activationTo) {
            $activationQuery->whereBetween(
                'created_at',
                [Carbon::parse($activationFrom), Carbon::parse($activationTo)]
            );
        }

        // Grouping logic
        $activationData = match ($activationGranularity) {
            'year' => $activationQuery
                ->selectRaw("YEAR(created_at) as period, COUNT(*) as total")
                ->groupByRaw("YEAR(created_at)")
                ->pluck('total', 'period'),

            'month' => $activationQuery
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
                ->groupBy('period')
                ->pluck('total', 'period'),

            default => $activationQuery
                ->selectRaw("DATE(created_at) as period, COUNT(*) as total")
                ->groupBy('period')
                ->pluck('total', 'period'),
        };

        $activationChartLabels = $activationData->keys();
        $activationChartValues = $activationData->values();

        // Check Complain data section
        abort_if(Gate::denies('check_complain_access'), 403);

        $range = $request->input('range', 'month');
        $statuses = ['Pending', 'processing', 'reject', 'solved'];

        $query = CheckComplain::query();

        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'half_month':
                $query->where('created_at', '>=', Carbon::now()->subDays(15));
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }

        $complains = $query->get();
        $grouped = $complains->groupBy(fn($item) =>
            $item->created_at->format('Y-m-d')
        );

        $labels = $grouped->keys();

        $dataByStatus = [];
        foreach ($statuses as $status) {
            $dataByStatus[$status] = $labels->map(fn($date) =>
                $grouped[$date]->where('status', $status)->count()
            );
        }

        // Stock count by model
        $user = Auth::user();
        $roleTitles = $user->roles()->pluck('title')->toArray();
        $isAdmin = in_array('Admin', $roleTitles);

        $stocks = CurrentStock::with(['productById.productModel', 'transferUser'])
            ->when($isAdmin, fn($q) => $q->whereNull('transfer_user_id'))
            ->when(!$isAdmin, fn($q) => $q->where('transfer_user_id', $user->id))
            ->get();

        $grouped = $stocks->groupBy(fn($item) =>
            optional($item->productById?->productModel)->product_model ?? 'Unknown'
        );

        $chartData = [];
        $totalStock = 0;

        foreach ($grouped as $modelName => $items) {
            $count = $items->count();
            $totalStock += $count;

            $chartData[] = [
                'label' => $modelName,
                'value' => $count,
            ];
        }

        // KYC Recharge Summary
        $totalsStatus = [
            'Pending' => KycRecharge::where('payment_status', 'pending')->count(),
            'Failed' => KycRecharge::where('payment_status', 'failed')->count(),
            'Completed' => KycRecharge::where('payment_status', 'completed')->count(),
        ];

        // Recharge Summary
        $totalsStatusRecharge = [
            'Pending' => RechargeRequest::where('payment_status', 'pending')->count(),
            'Failed' => RechargeRequest::where('payment_status', 'failed')->count(),
            'Success' => RechargeRequest::where('payment_status', 'success')->count(),
        ];

        // Complaint Summary
        $totalsStatusComplain = [
            'Pending' => CheckComplain::where('status', 'Pending')->count(),
            'Processing' => CheckComplain::where('status', 'processing')->count(),
            'Reject' => CheckComplain::where('status', 'reject')->count(),
            'Solved' => CheckComplain::where('status', 'solved')->count(),
        ];

        // Activation Summary (Admin / User specific)
        $user = Auth::user();
        $roleTitles = $user->roles()->pluck('title')->toArray();
        $isAdmin = in_array('Admin', $roleTitles);

        $query = DB::table('activation_requests')
            ->join('product_masters', 'activation_requests.product_id', '=', 'product_masters.id')
            ->join('product_models', 'product_masters.product_model_id', '=', 'product_models.id');

        if ($isAdmin) {
            $query->select(
                'product_models.product_model as model',
                'activation_requests.status',
                DB::raw('COUNT(*) as count')
            )
                ->groupBy('product_models.product_model', 'activation_requests.status');
        } else {
            $query->join('users', 'activation_requests.created_by_id', '=', 'users.id')
                ->select(
                    'product_models.product_model as model',
                    'activation_requests.status',
                    DB::raw('COUNT(*) as count'),
                    'users.name as creator_name'
                )
                ->where('activation_requests.created_by_id', $user->id)
                ->groupBy('product_models.product_model', 'activation_requests.status', 'users.name');
        }

        $combinedChartData = $query->get()->map(fn($item) => [
            'model'        => $item->model,
            'status'       => $item->status,
            'count'        => $item->count,
            'creator_name' => $item->creator_name ?? null
        ])->toArray();

        $totalActivations = array_sum(array_column($combinedChartData, 'count'));

        // Stock Transfer Summary
        $query = StockTransfer::with(['select_user', 'transferUser']);

        if (!auth()->user()->is_admin) {
            $query->where('transfer_id', auth()->id());
        }

        $transfers = $query
            ->selectRaw('select_user_id, transfer_id, COUNT(*) as total')
            ->groupBy('select_user_id', 'transfer_id')
            ->get();

        if (auth()->user()->is_admin) {
            $transferLabels = $transfers->map(fn($t) =>
                ($t->transferUser->name ?? 'Unknown') . " → " .
                ($t->select_user->name ?? 'Unknown')
            );
        } else {
            $transferLabels = $transfers->map(fn($t) =>
                $t->select_user->name ?? 'Unknown'
            );
        }

        $transferCounts = $transfers->pluck('total');

        // FINAL RETURN
        return view('home', compact(
            'userRole', 'totals', 'stockData', 'chartLabels', 'chartValues',
            'timeFilter', 'selectedRoleType', 'selectedUserId', 'users',
            'activationStatus', 'activationFrom', 'activationTo', 'activationGranularity',
            'activationChartLabels', 'activationChartValues', 'chartData',
            'totalStock', 'totalActivations', 'combinedChartData',
            'transferLabels', 'transferCounts', 'totalsStatus',
            'totalsStatusRecharge', 'totalsStatusComplain',
            
            // NEW INVESTOR SUMMARY VARIABLES
            'totalInvestors', 'totalInvestmentAmount',
            'totalPendingWithdraw', 'totalApprovedWithdraw',
            'investorChart', 'invFilter', 'invFrom', 'invTo'
        ));
    }


    // ---------------------------------------------
    // DO NOT MODIFY — Original Methods Unchanged
    // ---------------------------------------------
    
    public function getCustomerVehicleData(Request $request)
    {
        $vehicleChartData = AddCustomerVehicle::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'label' => $item->status ?? 'Unknown',
                'value' => $item->total,
            ]);

        $vehicleTotalCount = $vehicleChartData->sum('value');

        return view('home', [
            'vehicleChartData' => $vehicleChartData,
            'vehicleTotalCount' => $vehicleTotalCount,
        ]);
    }

    private function getUserTotals($user, $userRole, $roles)
    {
        if (!in_array($userRole, ['Admin', 'CNF', 'Dealer', 'Distributer'])) {
            return [];
        }

        if ($userRole === 'Admin') {
            return [
                'CNF' => DB::table('role_user')->where('role_id', $roles['CNF'])->count(),
                'Dealer' => DB::table('role_user')->where('role_id', $roles['Dealer'])->count(),
                'Distributer' => DB::table('role_user')->where('role_id', $roles['Distributer'])->count(),
                'Customer' => DB::table('role_user')->where('role_id', $roles['Customer'])->count(),
            ];
        }

        $createdUserIds = User::where('created_by_id', $user->id)->pluck('id');

        return [
            'CNF' => DB::table('role_user')->where('role_id', $roles['CNF'])->whereIn('user_id', $createdUserIds)->count(),
            'Dealer' => DB::table('role_user')->where('role_id', $roles['Dealer'])->whereIn('user_id', $createdUserIds)->count(),
            'Distributer' => DB::table('role_user')->where('role_id', $roles['Distributer'])->whereIn('user_id', $createdUserIds)->count(),
            'Customer' => DB::table('role_user')->where('role_id', $roles['Customer'])->whereIn('user_id', $createdUserIds)->count(),
        ];
    }


    // ---------------------------------------------
    // DO NOT MODIFY — Original Methods Unchanged
    // ---------------------------------------------
    
    
public function dashboardData(Request $request)
{
    $filter = $request->get('filter', 'all'); 
    $from   = $request->get('from_date');
    $to     = $request->get('to_date');

    $dateRange = $this->computeDateRange($filter, $from, $to);

    // ===================== Base Queries =====================
    $registrationsQ = Registration::query();
    $investmentsQ   = Investment::query();
    $withdrawalsQ   = WithdrawalRequest::query();

    if ($dateRange) {
        $registrationsQ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
        $investmentsQ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
        $withdrawalsQ->whereBetween('created_at', [$dateRange['from'], $dateRange['to']]);
    }

    // ===================== Investor Stats =====================
    $totalVerified = (clone $registrationsQ)->where('kyc_status', 'Verified')->count();
    $totalNotVerified = (clone $registrationsQ)->whereIn('kyc_status', ['Pending', 'Submitted'])->count();

    $kycPending   = (clone $registrationsQ)->where('kyc_status', 'Pending')->count();
    $kycSubmitted = (clone $registrationsQ)->where('kyc_status', 'Submitted')->count();
    $kycVerified  = $totalVerified;

    // ===================== Investment Stats =====================
    $totalInvestmentAmount = (clone $investmentsQ)->sum('principal_amount');

    // ===================== Withdrawal Stats =====================
    $withdrawRequested = (clone $withdrawalsQ)->sum('amount');
    $withdrawApproved  = (clone $withdrawalsQ)->where('status', 'approved')->sum('amount');
    $withdrawPending   = (clone $withdrawalsQ)->where('status', 'pending')->sum('amount');
    $withdrawRejected  = (clone $withdrawalsQ)->where('status', 'rejected')->sum('amount');

    // ===================== Withdrawal List (Left Side List) =====================
    $withdrawList = (clone $withdrawalsQ)
        ->with(['select_investor.investor'])     // Registration → investor(User)
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get()
        ->map(function ($w) {
            return [
                'id'            => $w->id,
                'amount'        => $w->amount,
                'status'        => $w->status,
                'date'          => $w->created_at ? $w->created_at->format('d M Y') : '',
                'investor_name' => $w->select_investor->investor->name ?? 'Unknown Investor',
            ];
        });

    // ===================== Pie Chart Data =====================
    $chartData = [
        ['label' => 'Approved', 'value' => (float)$withdrawApproved],
        ['label' => 'Pending',  'value' => (float)$withdrawPending],
        ['label' => 'Rejected', 'value' => (float)$withdrawRejected],
    ];

    // ===================== Final JSON Return =====================
    return response()->json([
        'status' => 'success',
        'data'   => [
            'totalVerified'         => $totalVerified,
            'totalNotVerified'      => $totalNotVerified,

            'kyc' => [
                'pending'   => $kycPending,
                'submitted' => $kycSubmitted,
                'verified'  => $kycVerified,
                'total'     => $kycPending + $kycSubmitted + $kycVerified,
            ],

            'totalInvestmentAmount' => (float)$totalInvestmentAmount,

            'withdraw' => [
                'requested' => (float)$withdrawRequested,
                'approved'  => (float)$withdrawApproved,
                'pending'   => (float)$withdrawPending,
                'rejected'  => (float)$withdrawRejected,
            ],

            'withdrawList' => $withdrawList,  // ⭐ For left-side list

            'chartData' => $chartData,
            'dateRange' => $dateRange
        ]
    ]);
}


    /**
     * Compute from/to based on filter & optional custom dates
     *
     * @param string $filter
     * @param string|null $from
     * @param string|null $to
     * @return array|null
     */
private function computeDateRange($filter, $from = null, $to = null)
{
    $today = Carbon::today();

    switch ($filter) {
        case 'today':
            return ['from' => $today->startOfDay(), 'to' => $today->endOfDay()];

        case 'yesterday':
            $y = $today->copy()->subDay();
            return ['from' => $y->startOfDay(), 'to' => $y->endOfDay()];

        case 'this_week':
            return ['from' => $today->startOfWeek(), 'to' => $today->endOfWeek()];

        case 'this_month':
            return ['from' => $today->startOfMonth(), 'to' => $today->endOfMonth()];

        case 'last_3_month':
            return ['from' => $today->copy()->subMonths(3)->startOfDay(), 'to' => Carbon::now()->endOfDay()];

        case 'last_6_month':
            return ['from' => $today->copy()->subMonths(6)->startOfDay(), 'to' => Carbon::now()->endOfDay()];

        case 'last_9_month':
            return ['from' => $today->copy()->subMonths(9)->startOfDay(), 'to' => Carbon::now()->endOfDay()];

        case 'this_year':
            return ['from' => $today->startOfYear(), 'to' => $today->endOfYear()];

        case 'custom':
            try {
                return [
                    'from' => Carbon::parse($from)->startOfDay(),
                    'to'   => Carbon::parse($to)->endOfDay(),
                ];
            } catch (\Exception $e) {
                return null;
            }

        case 'all':
        default:
            return null;
    }
}
// ================= INDIA MAP: STATE COUNTS =================
public function getStateCounts()
{
    $data = Registration::select('state', DB::raw('COUNT(*) as total'))
        ->groupBy('state')
        ->pluck('total','state');

    return response()->json([
        'status' => 'success',
        'data'   => $data
    ]);
}

// ================= INDIA MAP: STATE REGISTRATION DETAIL =================
public function getStateRegistrations($state)
{
    $list = Registration::where('state', $state)
        ->select('id', 'father_name', 'city', 'pincode', 'kyc_status','created_at')
        ->orderBy('created_at','desc')
        ->limit(200)
        ->get();

    return response()->json([
        'status' => 'success',
        'state'  => $state,
        'data'   => $list
    ]);
}

}
