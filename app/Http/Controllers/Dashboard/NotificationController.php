<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);

        return Inertia::render('Dashboard/Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Get recent notifications (for dropdown).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $notifications  = auth()->user()->notifications()->latest()->limit(5)->get();
        $unreadCount    = auth()->user()->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
