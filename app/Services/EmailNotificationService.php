<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NotificationMail;
use App\Mail\TaskAssignedMail;
use App\Mail\TaskSubmittedMail;
use App\Mail\TaskReviewedMail;
use App\Mail\TaskCompletedMail;

class EmailNotificationService
{
    /**
     * Send notification email
     */
    public function sendNotificationEmail($user, $type, $title, $message, $data = [])
    {
        try {
            // Determine which email class to use
            $mailClass = $this->getMailClass($type);
            
            if ($mailClass) {
                Mail::to($user->email)->send(
                    new $mailClass($user, $title, $message, $data)
                );
            } else {
                // Fallback to generic notification email
                Mail::to($user->email)->send(
                    new NotificationMail($user, $title, $message, $type, $data)
                );
            }

            // Log that email was sent
            Log::info('Notification email sent', [
                'user_id' => $user->id,
                'type' => $type,
                'email' => $user->email
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send notification email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the appropriate mail class for the notification type
     */
    protected function getMailClass($type)
    {
        $mapping = [
            'task_assigned' => TaskAssignedMail::class,
            'task_submitted' => TaskSubmittedMail::class,
            'task_reviewed' => TaskReviewedMail::class,
            'task_completed' => TaskCompletedMail::class,
        ];

        return $mapping[$type] ?? null;
    }
}