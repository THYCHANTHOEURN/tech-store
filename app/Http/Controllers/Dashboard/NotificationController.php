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
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        // Better detection of request type - handle both Inertia and AJAX requests
        if (request()->ajax() && !request()->header('X-Inertia')) {
            return response()->json(['success' => true]);
        }

        // For Inertia requests, use a redirect to prevent the JSON response modal
        return redirect()->back();
    }
}
