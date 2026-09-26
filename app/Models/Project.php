<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Concerns\HasHashid;

class Project extends Model
{

    use HasHashid;

    protected $fillable = [
        'name',
        'description',
        'organization_id',
        'created_by',
        'leader_id',      // kept for backward compat (unused)
        'type',
        'invite_code',
        'status',
        'start_date',
        'deadline',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline'   => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function mainTasks()
    {
        return $this->hasMany(Task::class)
            ->where('task_type', 'main')
            ->whereNull('parent_task_id');
    }

    public function documents()
    {
        return $this->hasMany(ProjectDocument::class)->orderByDesc('created_at');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @deprecated Project leader concept removed. Kept for backward compat.
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    // ============================================
    // PROJECT MEMBERS RELATIONSHIPS
    // ============================================
    public function projectMembers()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->withPivot('role', 'status', 'joined_at', 'removed_at', 'removed_by')
                    ->withTimestamps();
    }

    public function activeMembers()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->wherePivot('status', 'active')
                    ->withPivot('role', 'joined_at');
    }

    public function removedMembers()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->wherePivot('status', 'removed')
                    ->withPivot('role', 'removed_at', 'removed_by');
    }

    public function pendingMembers()
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->wherePivot('status', 'pending')
                    ->withPivot('role');
    }

    public function membersByRole($role)
    {
        return $this->belongsToMany(User::class, 'project_members')
                    ->wherePivot('status', 'active')
                    ->wherePivot('role', $role);
    }

    // ============================================
    // HELPER METHODS
    // ============================================
    public function isMember($userId)
    {
        return $this->members()
                    ->where('user_id', $userId)
                    ->wherePivot('status', 'active')
                    ->exists();
    }

    public function isCreator($userId)
    {
        return $this->created_by === $userId;
    }

    /**
     * @deprecated Project leader concept removed.
     */
    public function isLeader($userId)
    {
        return $this->leader_id === $userId;
    }

    public function hasAccess($userId)
    {
        return $this->isCreator($userId) || $this->isMember($userId);
    }

    public function getMemberCountAttribute()
    {
        return $this->activeMembers()->count();
    }

    public function getTaskStatsAttribute()
    {
        // Main tasks only — sub-tasks roll up into them.
        $mainTasks = $this->mainTasks();
        return [
            'total'       => $mainTasks->count(),
            'todo'        => $mainTasks->where('status', 'todo')->count(),
            'in_progress' => $mainTasks->where('status', 'in_progress')->count(),
            'review'      => $mainTasks->where('status', 'review')->count(),
            'done'        => $mainTasks->where('status', 'done')->count(),
            'blocked'     => $mainTasks->where('status', 'blocked')->count(),
        ];
    }

    public function getCompletionPercentageAttribute()
    {
        $total = $this->mainTasks()->count();
        if ($total === 0) return 0;
        $completed = $this->mainTasks()->where('status', 'done')->count();
        return (int) round(($completed / $total) * 100);
    }

    public function getProgressColorAttribute()
    {
        $percentage = $this->completion_percentage;
        if ($percentage >= 80) return 'green';
        if ($percentage >= 50) return 'yellow';
        return 'red';
    }

    // ============================================
    // DATE ACCESSORS
    // ============================================

    /**
     * Whole days until the deadline.
     * Positive = days remaining. Zero = due today. Negative = days overdue.
     */
    public function getDaysUntilDeadlineAttribute(): int
    {
        if (!$this->deadline) return 0;
        return (int) now()->startOfDay()
            ->diffInDays($this->deadline->copy()->startOfDay(), false);
    }

    /**
     * Whole days elapsed since start_date.
     * Clamped to >= 0 so a future start_date doesn't produce a negative.
     */
    public function getDaysElapsedAttribute(): int
    {
        if (!$this->start_date) return 0;
        $days = $this->start_date->copy()->startOfDay()
            ->diffInDays(now()->startOfDay(), false);
        return (int) max(0, $days);
    }

    /**
     * Total duration in days from start_date to deadline.
     * Always >= 1 so it can't divide by zero in the timeline.
     */
    public function getDurationDaysAttribute(): int
    {
        if (!$this->start_date || !$this->deadline) return 0;
        $days = $this->start_date->copy()->startOfDay()
            ->diffInDays($this->deadline->copy()->startOfDay(), false);
        return (int) max(1, $days);
    }

    /**
     * Timeline completion percentage (0-100).
     * Uses elapsed / duration, clamped so it never overshoots.
     */
    public function getTimelinePercentAttribute(): int
    {
        if (!$this->start_date || !$this->deadline || $this->duration_days <= 0) {
            return 0;
        }
        $elapsed = max(0, min($this->days_elapsed, $this->duration_days));
        return (int) round(($elapsed / $this->duration_days) * 100);
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->deadline) return false;
        return now()->startOfDay()->greaterThan($this->deadline->copy()->startOfDay())
            && $this->status !== 'completed';
    }

    // ============================================
    // TYPE CHECKERS
    // ============================================
    public function isCompany(): bool
    {
        return $this->type === 'company';
    }

    public function isPersonal(): bool
    {
        return $this->type === 'personal';
    }

    // ============================================
    // BOOT
    // ============================================
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($project) {
            if (empty($project->invite_code)) {
                $project->invite_code = self::generateUniqueInviteCode();
            }
            if (empty($project->type)) {
                $project->type = 'personal';
            }
            if (empty($project->status)) {
                $project->status = 'active';
            }
        });

        static::created(function ($project) {
            if ($project->created_by) {
                if (!$project->members()->where('user_id', $project->created_by)->exists()) {
                    $project->members()->attach($project->created_by, [
                        'role'      => 'leader',
                        'status'    => 'active',
                        'joined_at' => now(),
                    ]);
                }
            }
        });
    }

    public static function generateUniqueInviteCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('invite_code', $code)->exists());
        return $code;
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopePersonal($query)
    {
        return $query->where('type', 'personal');
    }

    public function scopeCompany($query)
    {
        return $query->where('type', 'company');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    public function scopeWhereMember($query, $userId)
    {
        return $query->whereHas('members', function ($q) use ($userId) {
            $q->where('user_id', $userId)->wherePivot('status', 'active');
        });
    }

    public function scopeWhereCreator($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeWhereUserHasAccess($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhereHas('members', function ($memberQuery) use ($userId) {
                  $memberQuery->where('user_id', $userId)
                              ->wherePivot('status', 'active');
              });
        });
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now()->startOfDay())
                     ->where('status', '!=', 'completed');
    }

    public function scopeUpcomingDeadline($query)
    {
        return $query->whereBetween('deadline', [now(), now()->addDays(7)])
                     ->where('status', '!=', 'completed');
    }
}