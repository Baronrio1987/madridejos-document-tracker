<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Auth::user()->notifications()->with('document');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $isRead = $request->status === 'read';
            $query->where('is_read', $isRead);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('view', $notification);

        try {
            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notification as read: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            Auth::user()->notifications()->unread()->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking notifications as read: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()->unread()->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    public function getRecent()
    {
        $notifications = Auth::user()->notifications()
                              ->with('document')
                              ->latest()
                              ->limit(10)
                              ->get();

        return response()->json([
            'notifications' => $notifications,
        ]);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);

        try {
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting notification: ' . $e->getMessage(),
            ], 500);
        }
    }
}
