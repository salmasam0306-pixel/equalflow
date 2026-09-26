<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $title;
    public $message;
    public $type;
    public $data;

    public function __construct($user, $title, $message, $type, $data = [])
    {
        $this->user = $user;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "EqualFlow: {$this->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'user' => $this->user,
                'title' => $this->title,
                'message' => $this->message,
                'type' => $this->type,
                'data' => $this->data,
                'actionUrl' => $this->data['link'] ?? null,
                'actionText' => $this->data['action_text'] ?? 'View Details',
            ]
        );
    }
}