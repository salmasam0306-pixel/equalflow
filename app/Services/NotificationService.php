<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class NotificationService
{
    /**
     * Create + (optionally) email a notification.
     * The in-app record is always written first and is never rolled back
     * because of an email failure.
     */
    public function notify($userId, $type, $title, $message, $data = [])
    {
        try {
            $preference = NotificationPreference::where('user_id', $userId)
                ->where('type', $type)
                ->first();

            $inAppEnabled = $preference ? (bool) $preference->in_app_enabled : true;
            $emailEnabled = $preference ? (bool) $preference->email_enabled : true;

            // 1. In-app (must always succeed independently of email)
            $notification = null;
            if ($inAppEnabled) {
                $notification = $this->createInAppNotification($userId, $type, $title, $message, $data);
            }

            // 2. Email (best-effort, never blocks the in-app record)
            if ($emailEnabled) {
                $user = User::find($userId);
                if ($user && $user->email) {
                    try {
                        $this->sendEmailNotification($user, $type, $title, $message, $data);

                        if ($notification) {
                            $notification->update([
                                'is_email_sent' => true,
                                'email_sent_at' => now(),
                            ]);
                        }
                    } catch (\Throwable $e) {
                        Log::error('Email send failed (in-app notification preserved)', [
                            'user_id' => $userId,
                            'type'    => $type,
                            'error'   => $e->getMessage(),
                        ]);
                    }
                }
            }

            return $notification;
        } catch (\Throwable $e) {
            Log::error('Failed to send notification', [
                'user_id' => $userId,
                'type'    => $type,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Write the in-app notification row.
     */
    protected function createInAppNotification($userId, $type, $title, $message, $data = [])
    {
        try {
            return Notification::create([
                'user_id'   => $userId,
                'sender_id' => $data['sender_id'] ?? null,
                'type'      => $type,
                'title'     => $title,
                'message'   => $message,
                'link'      => $data['link'] ?? null,
                'icon'      => $data['icon'] ?? null,
                'color'     => $data['color'] ?? null,
                'is_read'   => false,
                'metadata'  => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create in-app notification', [
                'user_id' => $userId,
                'type'    => $type,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send the notification email via the generic template.
     */
    protected function sendEmailNotification($user, $type, $title, $message, $data = [])
    {
        if (config('app.env') === 'testing') {
            return;
        }

        $view = 'emails.notification';
        if (!View::exists($view)) {
            Log::warning('Email view not found: ' . $view);
            return;
        }

        $emailData = [
            'user'       => $user,
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'data'       => $data,
            'actionUrl'  => $data['link'] ?? null,
            'actionText' => $data['action_text'] ?? 'View Details',
        ];

        Mail::send($view, $emailData, function ($mail) use ($user, $title) {
            $mail->to($user->email)->subject("EqualFlow: {$title}");
        });

        Log::info('Notification email sent', [
            'user_id' => $user->id,
            'type'    => $type,
            'email'   => $user->email,
        ]);
    }

    public function getUnreadCount($userId)
    {
        try {
            return Notification::forUser($userId)->unread()->count();
        } catch (\Throwable $e) {
            Log::error('Failed to get unread count: ' . $e->getMessage());
            return 0;
        }
    }

    public function getNotifications($userId, $limit = 20, $offset = 0)
    {
        try {
            return Notification::forUser($userId)
                ->with(['sender'])
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get()
                ->map(function ($notification) {
                    $notification->time_ago = $notification->created_at?->diffForHumans() ?? 'Just now';
                    return $notification;
                });
        } catch (\Throwable $e) {
            Log::error('Failed to get notifications', [
                'user_id' => $userId,
                'error'   => $e->getMessage(),
            ]);

            if (config('app.debug')) {
                throw $e;
            }

            return collect();
        }
    }

    public function markAsRead($notificationId, $userId)
    {
        try {
            $notification = Notification::forUser($userId)->where('id', $notificationId)->first();
            if ($notification) {
                $notification->markAsRead();
                return true;
            }
            return false;
        } catch (\Throwable $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    public function markAllAsRead($userId)
    {
        try {
            return Notification::forUser($userId)->unread()->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to mark all as read: ' . $e->getMessage());
            return 0;
        }
    }

    public function deleteNotification($notificationId, $userId)
    {
        try {
            return Notification::forUser($userId)->where('id', $notificationId)->delete();
        } catch (\Throwable $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePreferences($userId, $type, $data)
    {
        try {
            return NotificationPreference::updateOrCreate(
                ['user_id' => $userId, 'type' => $type],
                [
                    'email_enabled'  => $data['email_enabled'] ?? true,
                    'in_app_enabled' => $data['in_app_enabled'] ?? true,
                ]
            );
        } catch (\Throwable $e) {
            Log::error('Failed to update preferences: ' . $e->getMessage());
            return null;
        }
    }

    public function getPreferences($userId)
    {
        try {
            return NotificationPreference::where('user_id', $userId)->get();
        } catch (\Throwable $e) {
            Log::error('Failed to get preferences: ' . $e->getMessage());
            return collect();
        }
    }

    public function createDefaultPreferences($userId)
    {
        try {
            $types = [
                'task_assigned', 'task_submitted', 'task_reviewed', 'task_completed',
                'task_overdue', 'project_created', 'member_invited', 'member_joined',
                'comment_added', 'mention', 'system',
            ];

            foreach ($types as $type) {
                NotificationPreference::firstOrCreate(
                    ['user_id' => $userId, 'type' => $type],
                    ['email_enabled' => true, 'in_app_enabled' => true]
                );
            }
        } catch (\Throwable $e) {
            Log::error('Failed to create default preferences: ' . $e->getMessage());
        }
    }

    public function sendTestNotification($userId)
    {
        return $this->notify(
            $userId,
            'system',
            'Test Notification',
            'This is a test notification to verify the system is working.',
            [
                'link'        => route('dashboard'),
                'action_text' => 'Go to Dashboard',
                'sender_id'   => null,
            ]
        );
    }

    public function getStats($userId)
    {
        try {
            $total  = Notification::forUser($userId)->count();
            $unread = Notification::forUser($userId)->unread()->count();

            return [
                'total'  => $total,
                'unread' => $unread,
                'read'   => max($total - $unread, 0),
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to get notification stats: ' . $e->getMessage());
            return ['total' => 0, 'unread' => 0, 'read' => 0];
        }
    }
}