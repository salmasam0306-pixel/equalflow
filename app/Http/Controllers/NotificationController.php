<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * JSON: list notifications for the authenticated user.
     * Route: GET /notifications/data
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $limit  = (int) $request->get('limit', 20);
        $offset = (int) $request->get('offset', 0);

        $notifications = $this->notificationService->getNotifications($userId, $limit, $offset);
        $unreadCount   = $this->notificationService->getUnreadCount($userId);
        $stats         = $this->notificationService->getStats($userId);

        return response()->json([
            'success'      => true,
            'data'         => $notifications,
            'unread_count' => $unreadCount,
            'total'        => $stats['total'],
        ]);
    }

    /**
     * JSON: unread count only.
     * Route: GET /notifications/unread-count
     */
    public function unreadCount()
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'success' => true,
            'count'   => $count,
        ]);
    }

    /**
     * JSON: mark a single notification as read.
     * Route: POST /notifications/{id}/read
     */
    public function markAsRead($id)
    {
        $result = $this->notificationService->markAsRead((int) $id, Auth::id());

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Notification marked as read' : 'Notification not found',
        ]);
    }

    /**
     * JSON: mark all as read.
     * Route: POST /notifications/mark-all-read
     */
    public function markAllAsRead()
    {
        $count = $this->notificationService->markAllAsRead(Auth::id());

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
            'count'   => $count,
        ]);
    }

    /**
     * JSON: delete a notification.
     * Route: DELETE /notifications/{id}
     */
    public function destroy($id)
    {
        $result = $this->notificationService->deleteNotification((int) $id, Auth::id());

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Notification deleted' : 'Notification not found',
        ]);
    }

    /**
     * JSON: get preferences.
     * Route: GET /notifications/preferences
     */
    public function preferences()
    {
        $preferences = $this->notificationService->getPreferences(Auth::id());

        return response()->json([
            'success' => true,
            'data'    => $preferences,
        ]);
    }

    /**
     * JSON: update preferences.
     * Route: POST /notifications/preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'type'           => 'required|string',
            'email_enabled'  => 'boolean',
            'in_app_enabled' => 'boolean',
        ]);

        $preference = $this->notificationService->updatePreferences(
            Auth::id(),
            $request->type,
            [
                'email_enabled'  => $request->boolean('email_enabled', true),
                'in_app_enabled' => $request->boolean('in_app_enabled', true),
            ]
        );

        return response()->json([
            'success' => true,
            'data'    => $preference,
            'message' => 'Preferences updated successfully',
        ]);
    }

    /**
     * Blade: notifications page.
     * Route: GET /notifications
     */
    public function page()
    {
        $userId = Auth::id();
        $stats  = $this->notificationService->getStats($userId);

        return view('notifications.index', [
            'unreadCount' => $stats['unread'],
            'total'       => $stats['total'],
        ]);
    }
}