<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasHashid;

class Task extends Model
{
    use HasHashid;
    
    // ============================================
    // STATUS CONSTANTS
    // ============================================
    const STATUS_TODO = 'todo';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW = 'review';
    const STATUS_DONE = 'done';
    const STATUS_BLOCKED = 'blocked';

    // ============================================
    // TASK TYPE CONSTANTS
    // ============================================
    const TYPE_MAIN = 'main';
    const TYPE_SUB = 'sub';

    // ============================================
    // PRIORITY CONSTANTS
    // ============================================
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    public static function getStatuses()
    {
        return [
            self::STATUS_TODO,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
            self::STATUS_DONE,
            self::STATUS_BLOCKED,
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_TODO => 'To Do',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_REVIEW => 'Ready for Review',
            self::STATUS_DONE => 'Done',
            self::STATUS_BLOCKED => 'Blocked',
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_TODO => 'yellow',
            self::STATUS_IN_PROGRESS => 'blue',
            self::STATUS_REVIEW => 'purple',
            self::STATUS_DONE => 'green',
            self::STATUS_BLOCKED => 'red',
        ];
    }

    public static function getStatusIcons()
    {
        return [
            self::STATUS_TODO => 'fa-clock',
            self::STATUS_IN_PROGRESS => 'fa-spinner',
            self::STATUS_REVIEW => 'fa-eye',
            self::STATUS_DONE => 'fa-check',
            self::STATUS_BLOCKED => 'fa-ban',
        ];
    }

    public static function getPriorities()
    {
        return [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH];
    }

    public static function getPriorityLabels()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
        ];
    }

    public static function getPriorityColors()
    {
        return [
            self::PRIORITY_LOW => 'gray',
            self::PRIORITY_MEDIUM => 'blue',
            self::PRIORITY_HIGH => 'red',
        ];
    }

    // ============================================
    // FILLABLE & CASTS
    // ============================================
    protected $fillable = [
        'parent_task_id',
        'task_type',
        'title',
        'description',
        'project_id',
        'department_id',
        'assigned_to',
        'assigned_by',
        'created_by',
        'status',
        'priority',
        'due_date',
        'start_date',
        'skills_required',
        'experience_level',
        'estimated_hours',
        'is_assigned',
        'assigned_at',
        'is_blocked',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'start_date' => 'date',
        'skills_required' => 'array',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_blocked' => 'boolean',
        'is_assigned' => 'boolean',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** The parent main task (null if this is a main task). */
    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /** Sub-tasks belonging to this task. */
    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function dependencies()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    public function dependents()
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_task_id');
    }

    public function dependsOnTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id')
            ->withPivot('dependency_type')
            ->withTimestamps();
    }

    public function dependentTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_task_id', 'task_id')
            ->withPivot('dependency_type')
            ->withTimestamps();
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function latestSubmission()
    {
        return $this->hasOne(TaskSubmission::class)->latest();
    }

    // ============================================
    // TASK TYPE HELPERS
    // ============================================
    public function isMain(): bool
    {
        return $this->task_type === self::TYPE_MAIN || is_null($this->parent_task_id);
    }

    public function isSub(): bool
    {
        return $this->task_type === self::TYPE_SUB || !is_null($this->parent_task_id);
    }

    public function isStandalone(): bool
    {
        return is_null($this->department_id);
    }

    /**
     * Determine whether a user is allowed to view this task.
     *
     * Visibility rules:
     *  - Principal          → can see every task in their organization
     *  - Creator / Assigner → can see tasks they created or assigned
     *  - Assignee           → can see their own assignments
     *  - Parent leader      → can see sub-tasks under their main task
     *  - Sub-task assignee  → can see their sub-task (and parent via separate check)
     *  - Department member  → can see sub-tasks owned by their department
     *  - Org member         → can see main tasks in their organization's projects
     *  - Personal project   → creator + members
     */
    public function isVisibleTo(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return false;
        }

        // Principal sees everything in their organization
        if ($user->isPrincipal()) {
            $org = $user->currentOrganization();
            if (!$org) {
                return false;
            }
            return $this->project && $this->project->organization_id === $org->id;
        }

        // Creator / assigner / assignee always see it
        if ($this->created_by === $user->id) return true;
        if ($this->assigned_by === $user->id) return true;
        if ($this->assigned_to === $user->id) return true;

        // For sub-tasks: the parent main-task leader sees them
        if ($this->isSub() && $this->parent && $this->parent->assigned_to === $user->id) {
            return true;
        }

        // For main tasks: leaders see their own sub-task's parent
        if ($this->isMain()) {
            if ($this->subtasks()->where('assigned_to', $user->id)->exists()) {
                return true;
            }
        }

        // Department members see sub-tasks owned by their department
        if ($this->department_id && $user->department_id === $this->department_id) {
            if ($this->isSub()) {
                return true;
            }
        }

        // Personal project: creator + invited members
        if ($this->project && $this->project->type === 'personal') {
            if ($this->project->created_by === $user->id) {
                return true;
            }
            if ($this->project->members()->where('user_id', $user->id)->exists()) {
                return true;
            }
        }

        // Company project: org members can see main tasks
        if ($this->project && $this->project->type === 'company' && $this->isMain()) {
            $org = $user->currentOrganization();
            if ($org && $this->project->organization_id === $org->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Derive main task status from its sub-tasks.
     */
    public function computeStatusFromSubtasks(): string
    {
        $subs = $this->subtasks;

        if ($subs->isEmpty()) {
            return $this->status;
        }

        if ($subs->every(fn ($s) => $s->status === self::STATUS_DONE)) {
            return self::STATUS_DONE;
        }

        if ($subs->contains(fn ($s) => in_array($s->status, [
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
            self::STATUS_BLOCKED,
        ]))) {
            return self::STATUS_IN_PROGRESS;
        }

        return self::STATUS_TODO;
    }

    // ============================================
    // DEPENDENCY METHODS
    // ============================================
    public function isBlocked()
    {
        foreach ($this->dependsOnTasks as $dependency) {
            if ($dependency->status !== self::STATUS_DONE) {
                return true;
            }
        }
        return false;
    }

    public function getBlockingTasks()
    {
        return $this->dependsOnTasks->filter(fn ($task) => $task->status !== self::STATUS_DONE);
    }

    public function getBlockingCount()
    {
        return $this->getBlockingTasks()->count();
    }

    public function getDependencyProgress()
    {
        $total = $this->dependsOnTasks()->count();
        if ($total === 0) return 100;
        $completed = $this->dependsOnTasks()->where('status', self::STATUS_DONE)->count();
        return round(($completed / $total) * 100);
    }

    public function getRecommendedStartDate()
    {
        $dependencies = $this->dependsOnTasks;
        if ($dependencies->isEmpty()) return null;

        $latestDueDate = null;
        foreach ($dependencies as $dep) {
            if ($dep->due_date) {
                if (!$latestDueDate || $dep->due_date > $latestDueDate) {
                    $latestDueDate = $dep->due_date;
                }
            }
        }
        if (!$latestDueDate) return null;
        return $latestDueDate->copy()->addDay();
    }

    public function getDependencySummary()
    {
        $total = $this->dependsOnTasks()->count();
        $completed = $this->dependsOnTasks()->where('status', self::STATUS_DONE)->count();
        $blocking = $this->getBlockingTasks()->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'blocking' => $blocking,
            'progress' => $total > 0 ? round(($completed / $total) * 100) : 100,
            'latest_due_date' => $this->getRecommendedStartDate(),
            'is_blocked' => $blocking > 0,
        ];
    }

    // ============================================
    // SUBMISSION METHODS
    // ============================================
    public function isSubmitted()
    {
        return $this->submissions()->exists();
    }

    public function isApproved()
    {
        return $this->submissions()->where('status', 'approved')->exists();
    }

    public function getLatestSubmissionStatus()
    {
        $submission = $this->latestSubmission;
        return $submission ? $submission->status : 'not_submitted';
    }

    public function getLatestSubmission()
    {
        return $this->latestSubmission;
    }

    public function getPendingSubmissionsCount()
    {
        return $this->submissions()->where('status', 'pending')->count();
    }

    public function getSubmissionsWithDetails()
    {
        return $this->submissions()
            ->with(['submitter', 'reviewer', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ============================================
    // STATUS HELPERS
    // ============================================
    public function canBeCompleted()
    {
        return !$this->isBlocked() && $this->status !== self::STATUS_DONE;
    }

    public function isAssigned()
    {
        return $this->is_assigned && $this->assigned_to !== null;
    }

    public function isOverdue()
    {
        if (!$this->due_date || $this->status === self::STATUS_DONE) return false;
        return $this->due_date < now();
    }

    public function getStatusColor()
    {
        return self::getStatusColors()[$this->status] ?? 'gray';
    }

    public function getStatusIcon()
    {
        return self::getStatusIcons()[$this->status] ?? 'fa-circle';
    }

    public function getStatusLabel()
    {
        return self::getStatusLabels()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPriorityColor()
    {
        return self::getPriorityColors()[$this->priority] ?? 'gray';
    }

    public function getPriorityLabel()
    {
        return self::getPriorityLabels()[$this->priority] ?? ucfirst($this->priority ?? 'medium');
    }

    public function isValidStatus($status)
    {
        return in_array($status, self::getStatuses());
    }

    public function setStatus($status)
    {
        if ($this->isValidStatus($status)) {
            $this->status = $status;
            return true;
        }
        return false;
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeMainTasks($query)
    {
        return $query->where('task_type', self::TYPE_MAIN)->whereNull('parent_task_id');
    }

    public function scopeSubtasks($query)
    {
        return $query->where('task_type', self::TYPE_SUB)->whereNotNull('parent_task_id');
    }

    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeWithoutDepartment($query)
    {
        return $query->whereNull('department_id');
    }

    public function scopeStandalone($query)
    {
        return $query->whereNull('department_id');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeInProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', self::STATUS_DONE);
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', self::STATUS_DONE);
    }

    public function scopeTodo($query)
    {
        return $query->where('status', self::STATUS_TODO);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeReview($query)
    {
        return $query->where('status', self::STATUS_REVIEW);
    }

    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    // ============================================
    // ATTRIBUTE ACCESSORS
    // ============================================
    public function getFormattedDueDateAttribute()
    {
        return $this->due_date ? $this->due_date->format('d M Y') : null;
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('d M Y') : null;
    }

    public function getDaysUntilDueAttribute()
    {
        if (!$this->due_date) return null;
        return now()->diffInDays($this->due_date, false);
    }

    public function getIsUrgentAttribute()
    {
        if (!$this->due_date || $this->status === self::STATUS_DONE) return false;
        return now()->diffInDays($this->due_date, false) <= 3;
    }

    public function getIsOverdueAttribute()
    {
        return $this->isOverdue();
    }

    /**
     * Sub-task progress for main tasks.
     * Returns ['total' => int, 'done' => int, 'percent' => int]
     */
    public function getSubtaskProgressAttribute(): array
    {
        $subs = $this->subtasks;
        $total = $subs->count();
        $done = $subs->where('status', self::STATUS_DONE)->count();
        $percent = $total > 0 ? round(($done / $total) * 100) : 0;

        return ['total' => $total, 'done' => $done, 'percent' => $percent];
    }
}