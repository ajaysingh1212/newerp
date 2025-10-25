<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAlert;
use Symfony\Component\HttpFoundation\Response;

class UserAlertApiController extends Controller
{
    // Only submit alert
    public function submit(Request $request)
    {
        $request->validate([
            'alert_text' => 'required|string|max:255',
            'alert_link' => 'nullable|string',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        // Always include user ID 1
        $userIds = array_unique(array_merge([1], $request->user_ids));

        $alert = UserAlert::create([
            'alert_text' => $request->alert_text,
            'alert_link' => $request->alert_link,
        ]);

        $alert->users()->sync($userIds);

        return response()->json([
            'message' => 'Alert submitted successfully.',
            'alert_id' => $alert->id,
        ], Response::HTTP_CREATED);
    }
}
