<?php

// app/Http/Controllers/Admin/AccountDeletionAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountDeletionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AccountDeletionAdminController extends Controller
{
    public function index()
    {
        $requests = AccountDeletionRequest::with('user')->latest()->paginate(20);
        return view('admin.deletion_requests.index', compact('requests'));
    }

    public function show($id)
    {
        $req = AccountDeletionRequest::with('user')->findOrFail($id);
        return view('admin.deletion_requests.show', compact('req'));
    }

    public function approve(Request $request, $id)
    {
        $req = AccountDeletionRequest::findOrFail($id);

        if($req->status !== 'pending'){
            return back()->with('warning','Request already processed.');
        }

        // optional admin note
        $req->admin_note = $request->input('admin_note');
        $req->status = 'approved';
        $req->approved_by = Auth::id();
        $req->approved_at = Carbon::now();
        $req->save();

        // Soft delete the user
        $user = User::find($req->user_id);
        if($user){
            $user->delete(); // uses SoftDeletes
        }

        // optional: notify user about approval via Notification / Mail

        return redirect()->route('admin.deletion.requests.index')->with('success','Account deletion approved and user soft-deleted.');
    }

    public function reject(Request $request, $id)
    {
        $req = AccountDeletionRequest::findOrFail($id);

        if($req->status !== 'pending'){
            return back()->with('warning','Request already processed.');
        }

        $req->status = 'rejected';
        $req->approved_by = Auth::id();
        $req->approved_at = Carbon::now();
        $req->admin_note = $request->input('admin_note');
        $req->save();

        // optional: notify user about rejection

        return redirect()->route('admin.deletion.requests.index')->with('success','Deletion request rejected.');
    }
}
