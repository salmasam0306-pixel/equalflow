<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $task;
    public $assigner;

    public function __construct($user, $title, $message, $data = [])
    {
        $this->user = $user;
        $this->task = $data['task'] ?? null;
        $this->assigner = $data['assigner'] ?? null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Task Assigned: {$this->task?->title ?? 'Task'}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-assigned',
            with: [
                'user' => $this->user,
                'task' => $this->task,
                'assigner' => $this->assigner,
            ]
        );
    }
}