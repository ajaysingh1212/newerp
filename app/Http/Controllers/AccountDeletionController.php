<?php

// app/Http/Controllers/AccountDeletionController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccountDeletionRequest;
use Illuminate\Support\Facades\Auth;

class AccountDeletionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:2000'
        ]);

        $user = Auth::user();

        // check if there's already pending request
        $existing = AccountDeletionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if($existing){
            return back()->with('warning', 'You already have a pending deletion request.');
        }

        AccountDeletionRequest::create([
            'user_id' => $user->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // optional: notify admins, send email etc.

        return back()->with('success', 'Account deletion request submitted. It will be processed after admin approval.');
    }
}
