<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all notifications for the user
     */
    public function index()
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('app.pages.notifications', compact('notifications'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = UserNotification::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
        
        $notification->update(['is_read' => true, 'read_at' => now()]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        // Redirect to the action URL if provided
        if ($notification->action_url) {
            return redirect($notification->action_url);
        }
        
        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Get unread notification count (for AJAX)
     */
    public function unreadCount()
    {
        $count = UserNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
}

