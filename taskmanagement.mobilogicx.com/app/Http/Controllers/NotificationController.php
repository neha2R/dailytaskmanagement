<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FirebaseNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kutia\Larafirebase\Facades\Larafirebase;

class NotificationController extends Controller
{
    public function updateDeviceToken(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'token' => 'required|string|max:255', // Enforce a maximum length to ensure data integrity
        ]);
    
        try {
            // Update the authenticated user's device token
            $user = User::find(Auth::user()->id);
            $user->update([
                'device_token' => $request->token,
            ]);
    
            // Return a success response
            return response()->json([
                'message' => 'Device token successfully updated.',
                'token' => $request->token,
            ]);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Failed to update device token', [
                'userId' => Auth::id(),
                'error' => $e->getMessage(),
            ]);
    
            // Return an error response
            return response()->json([
                'error' => 'An unexpected error occurred while updating the device token.',
            ], 500);
        }
    }
    // for using database notification in web
    public function fetchNotifications()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications->map(function ($notification) {
            return [
                'url'=>$notification->data['url'] ?? "",
                'title'=>$notification->data['title'] ?? "",
                'description'=>$notification->data['description'] ?? "",
                'created_at'=>Carbon::parse($notification->created_at)->diffForHumans() ?? ""
            ];
        });
        
        
        return response()->json(['notifications' => $notifications]);
    }

    public function clearNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Notifications cleared successfully']);
    }
}
