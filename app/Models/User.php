<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Concerns\HasHashid;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasHashid;
    use Notifiable;

    // ============================================
    // STATUS CONSTANTS
    // ============================================
    const STATUS_ACTIVE = 'active';
    const STATUS_OUTSTATION = 'outstation';
    const STATUS_ANNUAL_LEAVE = 'annual_leave';
    const STATUS_MEDICAL_LEAVE = 'medical_leave';

    // ============================================
    // JOB SCOPE CONSTANTS
    // ============================================
    const JOB_CIVIL_ENGINEER = 'civil_engineer';
    const JOB_STRUCTURAL_ENGINEER = 'structural_engineer';
    const JOB_DRAFTER = 'drafter';
    const JOB_IOW_ROAD = 'iow_road';
    const JOB_IOW_DRAINAGE = 'iow_drainage';
    const JOB_IOW_EARTHWORK = 'iow_earthwork';
    const JOB_IOW_WALL = 'iow_wall';
    const JOB_IOW_BRIDGE = 'iow_bridge';
    const JOB_CLERK = 'clerk';
    const JOB_PROJECT_MANAGER = 'project_manager';
    const JOB_SITE_SUPERVISOR = 'site_supervisor';
    const JOB_QUALITY_CONTROL = 'quality_control';
    const JOB_OTHER = 'other';

    // ============================================
    // ENGINEERING JOB SCOPES
    // Eligible to lead a main task.
    // ============================================
    public const ENGINEERING_JOB_SCOPES = [
        self::JOB_CIVIL_ENGINEER,
        self::JOB_STRUCTURAL_ENGINEER,
        self::JOB_IOW_ROAD,
        self::JOB_IOW_DRAINAGE,
        self::JOB_IOW_EARTHWORK,
        self::JOB_IOW_WALL,
        self::JOB_IOW_BRIDGE,
    ];

    // ============================================
    // JOB SCOPE → BASELINE SKILLS MAP
    // Used by getAllSkills() so AIService and WorkloadAnalyzer agree.
    // ============================================
    public const JOB_SCOPE_SKILLS = [
        self::JOB_CIVIL_ENGINEER      => ['Road Design', 'Drainage System', 'Earthworks', 'Slope Design'],
        self::JOB_STRUCTURAL_ENGINEER => ['Structural Design', 'Steel Structure', 'Reinforced Concrete'],
        self::JOB_DRAFTER             => ['AutoCAD', 'Civil 3D', 'Revit', 'MicroStation'],
        self::JOB_IOW_ROAD            => ['Road Design', 'Highway Engineering', 'Site Supervision'],
        self::JOB_IOW_DRAINAGE        => ['Drainage System', 'Water Reticulation', 'Sewerage System'],
        self::JOB_IOW_EARTHWORK       => ['Earthworks', 'Site Grading', 'Slope Design'],
        self::JOB_IOW_WALL            => ['Structural Design', 'Reinforced Concrete', 'Foundation Design'],
        self::JOB_IOW_BRIDGE          => ['Structural Design', 'Steel Structure', 'Foundation Design'],
        self::JOB_CLERK               => ['Documentation', 'Submissions', 'OSC', 'BOMBA', 'JPS', 'IWK'],
        self::JOB_PROJECT_MANAGER     => ['Project Management', 'Scheduling', 'Cost Estimation'],
        self::JOB_SITE_SUPERVISOR     => ['Site Supervision', 'Quality Control', 'Safety Management'],
        self::JOB_QUALITY_CONTROL     => ['Quality Control', 'Documentation', 'Inspection'],
    ];

    // ============================================
    // STATUS ARRAYS (Static Methods)
    // ============================================
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_OUTSTATION,
            self::STATUS_ANNUAL_LEAVE,
            self::STATUS_MEDICAL_LEAVE,
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_OUTSTATION => 'Outstation',
            self::STATUS_ANNUAL_LEAVE => 'Annual Leave',
            self::STATUS_MEDICAL_LEAVE => 'Medical Leave',
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_ACTIVE => 'green',
            self::STATUS_OUTSTATION => 'blue',
            self::STATUS_ANNUAL_LEAVE => 'yellow',
            self::STATUS_MEDICAL_LEAVE => 'red',
        ];
    }

    public static function getStatusIcons()
    {
        return [
            self::STATUS_ACTIVE => 'fa-circle',
            self::STATUS_OUTSTATION => 'fa-plane',
            self::STATUS_ANNUAL_LEAVE => 'fa-umbrella-beach',
            self::STATUS_MEDICAL_LEAVE => 'fa-notes-medical',
        ];
    }

    // ============================================
    // JOB SCOPE ARRAYS (Static Methods)
    // ============================================
    public static function getJobScopes()
    {
        return [
            self::JOB_CIVIL_ENGINEER,
            self::JOB_STRUCTURAL_ENGINEER,
            self::JOB_DRAFTER,
            self::JOB_IOW_ROAD,
            self::JOB_IOW_DRAINAGE,
            self::JOB_IOW_EARTHWORK,
            self::JOB_IOW_WALL,
            self::JOB_IOW_BRIDGE,
            self::JOB_CLERK,
            self::JOB_PROJECT_MANAGER,
            self::JOB_SITE_SUPERVISOR,
            self::JOB_QUALITY_CONTROL,
            self::JOB_OTHER,
        ];
    }

    public static function getJobScopeLabels()
    {
        return [
            self::JOB_CIVIL_ENGINEER => 'Civil Engineer',
            self::JOB_STRUCTURAL_ENGINEER => 'Structural Engineer',
            self::JOB_DRAFTER => 'Drafter / Draftsperson',
            self::JOB_IOW_ROAD => 'IOW - Road',
            self::JOB_IOW_DRAINAGE => 'IOW - Drainage',
            self::JOB_IOW_EARTHWORK => 'IOW - Earthwork',
            self::JOB_IOW_WALL => 'IOW - Wall / Retaining Structure',
            self::JOB_IOW_BRIDGE => 'IOW - Bridge',
            self::JOB_CLERK => 'Clerk / Documentation Specialist',
            self::JOB_PROJECT_MANAGER => 'Project Manager',
            self::JOB_SITE_SUPERVISOR => 'Site Supervisor',
            self::JOB_QUALITY_CONTROL => 'Quality Control',
            self::JOB_OTHER => 'Other',
        ];
    }

    public static function getJobScopeIcons()
    {
        return [
            self::JOB_CIVIL_ENGINEER => 'fa-hard-hat',
            self::JOB_STRUCTURAL_ENGINEER => 'fa-building',
            self::JOB_DRAFTER => 'fa-drafting-compass',
            self::JOB_IOW_ROAD => 'fa-road',
            self::JOB_IOW_DRAINAGE => 'fa-water',
            self::JOB_IOW_EARTHWORK => 'fa-mountain',
            self::JOB_IOW_WALL => 'fa-bricks',
            self::JOB_IOW_BRIDGE => 'fa-archway',
            self::JOB_CLERK => 'fa-file-alt',
            self::JOB_PROJECT_MANAGER => 'fa-tasks',
            self::JOB_SITE_SUPERVISOR => 'fa-user-tie',
            self::JOB_QUALITY_CONTROL => 'fa-clipboard-check',
            self::JOB_OTHER => 'fa-user',
        ];
    }

    /**
     * Labels for the engineering-eligible job scopes only.
     * Useful for select dropdowns where only engineers are allowed.
     */
    public static function getEngineeringJobScopeLabels(): array
    {
        $all = self::getJobScopeLabels();
        $out = [];
        foreach (self::ENGINEERING_JOB_SCOPES as $slug) {
            $out[$slug] = $all[$slug] ?? ucfirst(str_replace('_', ' ', $slug));
        }
        return $out;
    }

    // ============================================
    // FILLABLE & CASTS
    // ============================================
    protected $fillable = [
        'name',
        'email',
        'phone',
        'google_id',
        'password',
        'role',
        'job_scope',
        'specialties',
        'profile_picture',
        'status',
        'status_until',
        'status_note',
        'status_updated_at',
        'department_id',
        'is_profile_ready',
        'profile_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'specialties' => 'array',
            'status_until' => 'date',
            'status_updated_at' => 'datetime',
            'profile_completed_at' => 'datetime',
        ];
    }

    // ============================================
    // AUTO-UPDATE STATUS
    // ============================================
    public function checkAndUpdateStatus()
    {
        if ($this->status !== 'active' && $this->status_until) {
            if (now()->startOfDay() >= $this->status_until->startOfDay()) {
                $this->update([
                    'status' => self::STATUS_ACTIVE,
                    'status_until' => null,
                    'status_updated_at' => now(),
                    'status_note' => null,
                ]);
                return true;
            }
        }
        return false;
    }

    protected static function booted()
    {
        static::retrieved(function ($user) {
            $user->checkAndUpdateStatus();
        });
    }

    // ============================================
    // PROFILE PICTURE
    // ============================================
    public function getProfilePictureUrl()
    {
        if ($this->profile_picture) {
            return asset('storage/profile_pictures/' . $this->profile_picture);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1a2a4a&color=ffffff&size=100&font-size=0.5';
    }

    // ============================================
    // STATUS METHODS
    // ============================================
    public function getStatusColor(): string
    {
        $colors = self::getStatusColors();
        return $colors[$this->status] ?? 'gray';
    }

    public function getStatusLabel(): string
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? 'Unknown';
    }

    public function getStatusIcon(): string
    {
        $icons = self::getStatusIcons();
        return $icons[$this->status] ?? 'fa-circle';
    }

    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isOnLeave(): bool
    {
        return in_array($this->status, [self::STATUS_ANNUAL_LEAVE, self::STATUS_MEDICAL_LEAVE]);
    }

    public function isOutstation(): bool
    {
        return $this->status === self::STATUS_OUTSTATION;
    }

    // ============================================
    // JOB SCOPE METHODS
    // ============================================
    public function getJobScopeLabel(): ?string
    {
        if (!$this->job_scope) {
            return null;
        }

        $labels = self::getJobScopeLabels();
        return $labels[$this->job_scope]
            ?? ucfirst(str_replace('_', ' ', $this->job_scope));
    }

    public function getJobScopeIcon(): string
    {
        if (!$this->job_scope) {
            return 'fa-user';
        }

        $icons = self::getJobScopeIcons();
        return $icons[$this->job_scope] ?? 'fa-user';
    }

    public function hasJobScope(string $jobScope): bool
    {
        return $this->job_scope === $jobScope;
    }

    /**
     * Is this user an engineer (eligible to lead a main task)?
     */
    public function isEngineer(): bool
    {
        return in_array($this->job_scope, self::ENGINEERING_JOB_SCOPES, true);
    }

    /**
     * All skills this user "has" — from specialties and job scope.
     *
     * Used by AIService and WorkloadAnalyzer so both agree on what counts
     * as a member's skill. Task history is intentionally NOT included —
     * being assigned a task requiring a skill doesn't prove the user has it.
     */
    public function getAllSkills(): array
    {
        $skills = [];

        // 1. Explicitly-set specialties (Principal-assigned)
        if (!empty($this->specialties) && is_array($this->specialties)) {
            $skills = array_merge($skills, $this->specialties);
        }

        // 2. Job-scope baseline skills
        $skills = array_merge($skills, $this->getJobScopeSkills());

        return array_values(array_unique($skills));
    }

    /**
     * Baseline skills a job scope is expected to have.
     * Used by getAllSkills() as a fallback when specialties are sparse.
     */
    public function getJobScopeSkills(): array
    {
        if (!$this->job_scope) {
            return [];
        }

        return self::JOB_SCOPE_SKILLS[$this->job_scope] ?? [];
    }

    // ============================================
    // DEPARTMENT RELATIONSHIPS
    // ============================================
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_members')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    // ============================================
    // PROFILE SETUP METHODS
    // ============================================
    public function isProfileComplete(): bool
    {
        return $this->is_profile_ready
            && !is_null($this->job_scope)
            && !is_null($this->department_id)
            && !empty($this->specialties);
    }

    public function getProfileStatus(): string
    {
        if ($this->isProfileComplete()) {
            return 'complete';
        }

        if ($this->is_profile_ready) {
            return 'pending_principal';
        }

        return 'pending_basic';
    }

    public function getProfileStatusLabel(): string
    {
        $statuses = [
            'complete'          => '✅ Profile Complete',
            'pending_principal' => '⏳ Awaiting Principal Setup',
            'pending_basic'     => '📝 Basic Profile Pending',
        ];

        return $statuses[$this->getProfileStatus()] ?? 'Unknown';
    }

    public function getProfileStatusColor(): string
    {
        $colors = [
            'complete'          => 'green',
            'pending_principal' => 'yellow',
            'pending_basic'     => 'gray',
        ];

        return $colors[$this->getProfileStatus()] ?? 'gray';
    }

    // ============================================
    // DEPARTMENT HELPER METHODS
    // ============================================
    public function hasDepartment(): bool
    {
        return !is_null($this->department_id);
    }

    public function getDepartmentName(): ?string
    {
        return $this->department?->name;
    }

    public function getDepartmentMembers(): array
    {
        if (!$this->hasDepartment()) {
            return [];
        }

        return $this->department->members()
            ->where('users.id', '!=', $this->id)
            ->get()
            ->toArray();
    }

    public function isInDepartment(Department $department): bool
    {
        return $this->department_id === $department->id;
    }

    public function getTaskCount(): int
    {
        return Task::where('assigned_to', $this->id)->count();
    }

    public function getCompletedTaskCount(): int
    {
        return Task::where('assigned_to', $this->id)
            ->where('status', 'done')
            ->count();
    }

    public function getPendingTaskCount(): int
    {
        return Task::where('assigned_to', $this->id)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->count();
    }

    // ============================================
    // MAIN TASK LEADERSHIP HELPERS
    // ============================================
    public function getLedMainTasks()
    {
        return Task::where('assigned_to', $this->id)
            ->where('task_type', Task::TYPE_MAIN)
            ->whereNull('parent_task_id')
            ->where('status', '!=', Task::STATUS_DONE)
            ->get();
    }

    public function getLedMainTaskCount(): int
    {
        return Task::where('assigned_to', $this->id)
            ->where('task_type', Task::TYPE_MAIN)
            ->whereNull('parent_task_id')
            ->where('status', '!=', Task::STATUS_DONE)
            ->count();
    }

    public function isLeadingAnyMainTask(): bool
    {
        return Task::where('assigned_to', $this->id)
            ->where('task_type', Task::TYPE_MAIN)
            ->whereNull('parent_task_id')
            ->where('status', '!=', Task::STATUS_DONE)
            ->exists();
    }

    // ============================================
    // NOTIFICATION METHODS
    // ============================================
    public function unreadNotificationsCount()
    {
        try {
            return \App\Models\Notification::where('user_id', $this->id)
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    public function getUnreadNotifications($limit = 10)
    {
        return $this->notifications()
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function markAllNotificationsAsRead()
    {
        return $this->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function organizationMembers(): HasMany
    {
        return $this->hasMany(OrganizationMember::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_members');
    }

    public function currentOrganization()
    {
        return $this->organizations()->first();
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * @deprecated Project-leader concept removed.
     * Kept as a relation only for backward compat with existing DB column.
     */
    public function ledProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'leader_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function ledMainTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to')
            ->where('task_type', Task::TYPE_MAIN)
            ->whereNull('parent_task_id');
    }

    public function assignedSubtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to')
            ->where('task_type', Task::TYPE_SUB)
            ->whereNotNull('parent_task_id');
    }

    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function personalProjects()
    {
        return $this->belongsToMany(Project::class, 'project_members');
    }

    // ============================================
    // ROLE CHECKS
    // ============================================
    public function isPrincipal(): bool
    {
        return $this->role === 'principal';
    }

    public function isLeader(): bool
    {
        return $this->role === 'leader';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isPersonal(): bool
    {
        return $this->role === 'personal';
    }

    public function isCompanyMember(): bool
    {
        return in_array($this->role, ['principal', 'leader', 'member']);
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeOnLeave($query)
    {
        return $query->whereIn('status', [self::STATUS_ANNUAL_LEAVE, self::STATUS_MEDICAL_LEAVE]);
    }

    public function scopeOutstation($query)
    {
        return $query->where('status', self::STATUS_OUTSTATION);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeWithoutDepartment($query)
    {
        return $query->whereNull('department_id');
    }

    public function scopeWithDepartment($query)
    {
        return $query->whereNotNull('department_id');
    }

    public function scopeProfileReady($query)
    {
        return $query->where('is_profile_ready', true);
    }

    public function scopeProfilePending($query)
    {
        return $query->where('is_profile_ready', false);
    }

    public function scopeWithJobScope($query, $jobScope)
    {
        return $query->where('job_scope', $jobScope);
    }

    /**
     * Scope: only users whose job_scope is an engineering role.
     * Use on the leader picker so non-engineers can't be selected.
     */
    public function scopeEngineers($query)
    {
        return $query->whereIn('job_scope', self::ENGINEERING_JOB_SCOPES);
    }
}