<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddCustomerVehicle;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CustomerVehicle;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VehicleSharingController extends Controller
{
public function store(Request $request)
{
    // Base validation
    $request->validate([
        'vehicle_ids'      => 'required|array',
        'role'             => 'required|string',
        'status'           => 'required|string',
        'existing_user_id' => 'nullable|exists:users,id',
    ]);

    // Additional validation only if a new user is being created
    if (!$request->filled('existing_user_id')) {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
    }

    // Use existing user or create new one
    if ($request->filled('existing_user_id')) {
        $user = User::findOrFail($request->existing_user_id);
    } else {
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'status'        => $request->status,
            'created_by_id' => auth()->id(),
        ]);

        // Attach role
        $role = Role::firstOrCreate(['title' => $request->role]);
        $user->roles()->attach($role->id);
    }

    // Save vehicle sharing entries
    foreach ($request->vehicle_ids as $vehicleId) {
        DB::table('vehicle_sharing')->insert([
            'vehicle_id'      => $vehicleId,
            'sharing_user_id' => $user->id,
            'created_by'      => auth()->id(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    return redirect()->back()->with('success', 'Vehicles shared successfully.');
}



    public function mySharedVehicles()
    {
        $userId = Auth::id();

        // Fetch all vehicles shared with this user
        $vehicles = DB::table('vehicle_sharing as vs')
            ->join('add_customer_vehicles as v', 'vs.vehicle_id', '=', 'v.id')
            ->join('users as owner', 'v.user_id', '=', 'owner.id')
            ->where('vs.sharing_user_id', $userId)
            ->select(
                'v.*',
                'owner.name as owner_name',
                'owner.email as owner_email',
                'vs.created_at as shared_at'
            )
            ->get();
        dd($vehicles);
        return view('admin.addCustomerVehicles.index', compact('vehicles'));
    }

}
