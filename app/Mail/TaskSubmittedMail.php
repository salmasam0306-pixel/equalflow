<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $task;
    public $submission;

    public function __construct($user, $title, $message, $data = [])
    {
        $this->user = $user;
        $this->task = $data['task'] ?? null;
        $this->submission = $data['submission'] ?? null;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Task Submitted: {$this->task?->title ?? 'Task'}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task-submitted',
            with: [
                'user' => $this->user,
                'task' => $this->task,
                'submission' => $this->submission,
            ]
        );
    }
}