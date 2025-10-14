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

    // Additional validation only if creating a new user
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

    $alreadyShared = [];
    $sharedCount = 0;

    foreach ($request->vehicle_ids as $vehicleId) {
        $exists = DB::table('vehicle_sharing')
                    ->where('vehicle_id', $vehicleId)
                    ->where('sharing_user_id', $user->id)
                    ->exists();

        if ($exists) {
            $alreadyShared[] = $vehicleId; // collect IDs already shared
        } else {
            DB::table('vehicle_sharing')->insert([
                'vehicle_id'      => $vehicleId,
                'sharing_user_id' => $user->id,
                'created_by'      => auth()->id(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $sharedCount++;
        }
    }

    // Prepare message
    $message = '';
    if ($sharedCount) {
        $message .= "Vehicles shared successfully.";
    }
    if (!empty($alreadyShared)) {
        $message .= " Some vehicles were already shared with this user and were skipped.";
    }

    return redirect()->back()->with('success', $message);
}



public function remove(Request $request)
{
    $request->validate([
        'vehicle_id' => 'required|exists:add_customer_vehicles,id',
        'sharing_user_id' => 'required|exists:users,id',
    ]);

    $deleted = DB::table('vehicle_sharing')
        ->where('vehicle_id', $request->vehicle_id)
        ->where('sharing_user_id', $request->sharing_user_id)
        ->delete();

    if ($deleted) {
        return response()->json(['success' => true]);
    } else {
        return response()->json(['success' => false, 'message' => 'Sharing record not found.']);
    }
}

}
