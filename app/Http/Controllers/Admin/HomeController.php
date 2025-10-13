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
        $chartValues = $stockData->groupBy('product.sku')->map(fn($items) => $items->count())->values();

        // Activation request filters
        $activationStatus = $request->input('activation_status');
        $activationFrom = $request->input('activation_from');
        $activationTo = $request->input('activation_to');
        $activationGranularity = $request->input('activation_granularity', 'day');

        $activationQuery = ActivationRequest::query();

        if ($activationStatus) {
            $activationQuery->where('status', $activationStatus);
        }

        if ($activationFrom && $activationTo) {
            $activationQuery->whereBetween('created_at', [Carbon::parse($activationFrom), Carbon::parse($activationTo)]);
        }

        // Activation chart grouping
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

        // Check Complain data

      abort_if(\Gate::denies('check_complain_access'), 403);

    $range = $request->input('range', 'month'); // Default to 'month'
    $statuses = ['Pending', 'processing', 'reject', 'solved'];

    // ✅ FIX: Define query first
    $query = CheckComplain::query();

    // ✅ Apply date range filter
    switch ($range) {
        case 'today':
            $query->whereDate('created_at', Carbon::today());
            break;
        case 'week':
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
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

    // ✅ Get filtered complaints
    $complains = $query->get();

    // ✅ Group by date
    $grouped = $complains->groupBy(function ($item) {
        return $item->created_at->format('Y-m-d');
    });

    $labels = $grouped->keys();

    // ✅ Group counts by status per date
    $dataByStatus = [];
    foreach ($statuses as $status) {
        $dataByStatus[$status] = $labels->map(function ($date) use ($grouped, $status) {
            return $grouped[$date]->where('status', $status)->count();
        });
    }


$user = Auth::user();

// Get all role titles of the user
$roleTitles = $user->roles()->pluck('title')->toArray();

// Check if user has Admin role
$isAdmin = in_array('Admin', $roleTitles);

// Fetch stocks based on role
$stocks = CurrentStock::with(['productById.productModel', 'transferUser'])
    ->when($isAdmin, function ($query) {
        $query->whereNull('transfer_user_id'); // Admin: stocks with null transfer_user_id
    }, function ($query) use ($user) {
        $query->where('transfer_user_id', $user->id); // Non-admin: stocks assigned to user
    })
    ->get();

// Group stocks by product model name
$grouped = $stocks->groupBy(function ($item) {
    return optional($item->productById?->productModel)->product_model ?? 'Unknown';
});

// Prepare chart data
$chartData = [];
$totalStock = 0;

foreach ($grouped as $modelName => $items) {
    $count = $items->count(); // You can use sum(quantity) if needed
    $totalStock += $count;

    $chartData[] = [
        'label' => $modelName,
        'value' => $count,
    ];
}




   $user = Auth::user();

// Get all role titles of the user
$roleTitles = $user->roles()->pluck('title')->toArray();

// Check if user is admin
$isAdmin = in_array('Admin', $roleTitles);

// Base query
$query = DB::table('activation_requests')
    ->join('product_masters', 'activation_requests.product_id', '=', 'product_masters.id')
    ->join('product_models', 'product_masters.product_model_id', '=', 'product_models.id');

if ($isAdmin) {
    // Admin: All data (no creator name)
    $query->select(
        'product_models.product_model as model',
        'activation_requests.status',
        DB::raw('COUNT(*) as count')
    )
    ->groupBy('product_models.product_model', 'activation_requests.status');
} else {
    // Non-admin: Only their own data + creator name
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

$combinedChartData = $query->get()->map(function ($item) {
    return [
        'model'        => $item->model,
        'status'       => $item->status,
        'count'        => $item->count,
        'creator_name' => $item->creator_name ?? null
    ];
})->toArray();

$totalActivations = array_sum(array_column($combinedChartData, 'count'));

    // Debug - check role and activation data

$query = StockTransfer::with(['select_user', 'transferUser']);

// Agar admin hai -> sab dikhaye
if (!auth()->user()->is_admin) {
    // normal user ke liye filter (jisne transfer kiya)
    $query->where('transfer_id', auth()->id());
}

$transfers = $query
    ->selectRaw('select_user_id, transfer_id, COUNT(*) as total')
    ->groupBy('select_user_id', 'transfer_id')
    ->get();

// Admin ke liye -> "Kisne Kisko"
if (auth()->user()->is_admin) {
    $transferLabels = $transfers->map(function ($t) {
        $from = $t->transferUser ? $t->transferUser->name : 'Unknown';
        $to   = $t->select_user ? $t->select_user->name : 'Unknown';
        return $from . ' → ' . $to;
    });
} else {
    // Normal user -> sirf "Kisko"
    $transferLabels = $transfers->map(function ($t) {
        return $t->select_user ? $t->select_user->name : 'Unknown';
    });
}

$transferCounts = $transfers->pluck('total');


        return view('home',[
    'labels' => $labels,
    'dataByStatus' => $dataByStatus,
    'statuses' => $statuses,
    'range' => $range,
     
], compact(
            'userRole', 'totals', 'stockData', 'chartLabels', 'chartValues',
            'timeFilter', 'selectedRoleType', 'selectedUserId', 'users',
            'activationStatus', 'activationFrom', 'activationTo', 'activationGranularity',
            'activationChartLabels', 'activationChartValues','chartData','totalStock', 'totalActivations', 'combinedChartData','transferLabels','transferCounts'
        ));


    }

    public function getCustomerVehicleData(Request $request)
{
    // Fetch vehicle status and count
    $vehicleChartData = AddCustomerVehicle::select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get()
        ->map(function ($item) {
            return [
                'label' => $item->status ?? 'Unknown',
                'value' => $item->total,
            ];
        });

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
        } else {
            $createdUserIds = User::where('created_by_id', $user->id)->pluck('id');

            return [
                'CNF' => DB::table('role_user')->where('role_id', $roles['CNF'])->whereIn('user_id', $createdUserIds)->count(),
                'Dealer' => DB::table('role_user')->where('role_id', $roles['Dealer'])->whereIn('user_id', $createdUserIds)->count(),
                'Distributer' => DB::table('role_user')->where('role_id', $roles['Distributer'])->whereIn('user_id', $createdUserIds)->count(),
                'Customer' => DB::table('role_user')->where('role_id', $roles['Customer'])->whereIn('user_id', $createdUserIds)->count(),
            ];
        }
    }


    public function productPieChart()
{
   

    return view('home', compact('chartData'));
}
}
