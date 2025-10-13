<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAlert;
use Symfony\Component\HttpFoundation\Response;

class UserAlertApiController extends Controller
{
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


    // ✅ Fetch alerts by user_id
    public function fetch($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['count' => 0, 'html' => '<div class="text-center">User not found</div>'], 404);
        }

        $alerts = $user->userUserAlerts()->withPivot('read')->latest()->limit(10)->get();

        $html = '';
        foreach ($alerts as $alert) {
            $html .= '<div class="dropdown-item">';
            $html .= '<a href="' . ($alert->alert_link ?? '#') . '" target="_blank">';
            $html .= !$alert->pivot->read ? '<strong>' : '';
            $html .= e($alert->alert_text);
            $html .= !$alert->pivot->read ? '</strong>' : '';
            $html .= '</a></div>';
        }

        if ($alerts->isEmpty()) {
            $html = '<div class="text-center">No alerts</div>';
        }

        return response()->json([
            'count' => $user->userUserAlerts()->wherePivot('read', false)->count(),
            'html' => $html,
        ]);
    }

    // ✅ Mark alerts as read
    public function markAsRead($user_id)
    {
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $alerts = $user->userUserAlerts()->wherePivot('read', false)->get();
        foreach ($alerts as $alert) {
            $alert->pivot->read = true;
            $alert->pivot->save();
        }

        return response()->json(['message' => 'All alerts marked as read.']);
    }
    
    
    // ✅ Fetch all alerts (no limit, full data)
    public function fetchAll()
    {
        $alert = UserAlert::with('users:id,name')
                          ->latest()
                          ->first(); // 👈 Only latest alert
    
        if (!$alert) {
            return response()->json(['data' => []]); // no alerts yet
        }
    
        $data = [
            'id' => $alert->id,
            'alert_text' => $alert->alert_text,
            'alert_link' => $alert->alert_link,
            'created_at' => $alert->created_at ? $alert->created_at->format('d-m-Y h:i A') : null,
            'updated_at' => $alert->updated_at ? $alert->updated_at->format('d-m-Y h:i A') : null,
            'users' => $alert->users
                ->filter(fn($user) => $user->id !== 1)
                ->map(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                ])
                ->values(),
        ];
    
        return response()->json(['data' => [$data]]);
    }






}
