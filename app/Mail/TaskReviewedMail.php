<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskReviewedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $task;
    public $status;

    public function __construct($user, $title, $message, $data = [])
    {
        $this->user = $user;
        $this->task = $data['task'] ?? null;
        $this->status = $data['status'] ?? 'reviewed';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Task {$this->status}: {$this->task?->title ?? 'Task'}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-reviewed',
            with: [
                'user' => $this->user,
                'task' => $this->task,
                'status' => $this->status,
            ]
        );
    }
}