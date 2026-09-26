<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'sender_id',
        'type',
        'title',
        'message',
        'link',
        'icon',
        'color',
        'is_read',
        'is_email_sent',
        'read_at',
        'email_sent_at',
        'metadata',
    ];

    protected $casts = [
        'is_read'       => 'boolean',
        'is_email_sent' => 'boolean',
        'read_at'       => 'datetime',
        'email_sent_at' => 'datetime',
        'metadata'      => 'array',
    ];

    /**
     * Always expose time_ago in JSON — the notifications blade reads
     * notification.time_ago directly.
     */
    protected $appends = ['time_ago'];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Scopes

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helpers

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at
            ? $this->created_at->diffForHumans()
            : 'Just now';
    }

    public function getIconClass()
    {
        $icons = [
            'task_assigned'   => 'fa-tasks text-blue-500',
            'task_submitted'  => 'fa-upload text-purple-500',
            'task_reviewed'   => 'fa-check-circle text-green-500',
            'task_completed'  => 'fa-check-double text-emerald-500',
            'task_overdue'    => 'fa-exclamation-triangle text-red-500',
            'project_created' => 'fa-folder-plus text-indigo-500',
            'member_invited'  => 'fa-user-plus text-cyan-500',
            'member_joined'   => 'fa-user-check text-green-500',
            'comment_added'   => 'fa-comment text-yellow-500',
            'mention'         => 'fa-at text-pink-500',
            'system'          => 'fa-bell text-gray-500',
        ];

        return $icons[$this->type] ?? 'fa-bell text-gray-500';
    }
}