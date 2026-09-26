<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasHashid;

class TaskSubmission extends Model
{
    use HasHashid;
    
    protected $fillable = [
        'task_id',
        'submitted_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'status',
        'review_notes',
        'reviewed_by',
        'submitted_at',
        'reviewed_at',
        'version'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'file_size' => 'integer',
    ];

    // ============================================
    // STATUS CONSTANTS
    // ============================================
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISION_REQUESTED = 'revision_requested';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_REVISION_REQUESTED,
        ];
    }

    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_REVISION_REQUESTED => 'Revision Requested',
        ];
    }

    public static function getStatusColors()
    {
        return [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_REVISION_REQUESTED => 'orange',
        ];
    }

    public static function getStatusIcons()
    {
        return [
            self::STATUS_PENDING => 'fa-clock',
            self::STATUS_APPROVED => 'fa-check',
            self::STATUS_REJECTED => 'fa-times',
            self::STATUS_REVISION_REQUESTED => 'fa-pencil-alt',
        ];
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskSubmissionComment::class, 'submission_id')->orderBy('created_at', 'asc');
    }

    // ============================================
    // STATUS HELPER METHODS
    // ============================================

    public function getStatusLabel()
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        return self::getStatusColors()[$this->status] ?? 'gray';
    }

    public function getStatusIcon()
    {
        return self::getStatusIcons()[$this->status] ?? 'fa-circle';
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isRevisionRequested()
    {
        return $this->status === self::STATUS_REVISION_REQUESTED;
    }

    public function isReviewed()
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_REVISION_REQUESTED]);
    }

    // ============================================
    // FILE HELPER METHODS
    // ============================================

    /**
     * Get formatted file size
     */
    public function getFileSizeFormatted()
    {
        $bytes = $this->file_size;
        
        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return round($bytes / 1048576, 2) . ' MB';
        }
    }

    /**
     * Get file extension
     */
    public function getFileExtension()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get file icon based on extension
     */
    public function getFileIcon()
    {
        $ext = strtolower($this->getFileExtension());
        
        $icons = [
            'pdf' => ['icon' => 'fa-file-pdf', 'class' => 'pdf'],
            'doc' => ['icon' => 'fa-file-word', 'class' => 'doc'],
            'docx' => ['icon' => 'fa-file-word', 'class' => 'doc'],
            'xls' => ['icon' => 'fa-file-excel', 'class' => 'excel'],
            'xlsx' => ['icon' => 'fa-file-excel', 'class' => 'excel'],
            'ppt' => ['icon' => 'fa-file-powerpoint', 'class' => 'powerpoint'],
            'pptx' => ['icon' => 'fa-file-powerpoint', 'class' => 'powerpoint'],
            'jpg' => ['icon' => 'fa-file-image', 'class' => 'image'],
            'jpeg' => ['icon' => 'fa-file-image', 'class' => 'image'],
            'png' => ['icon' => 'fa-file-image', 'class' => 'image'],
            'gif' => ['icon' => 'fa-file-image', 'class' => 'image'],
            'svg' => ['icon' => 'fa-file-image', 'class' => 'image'],
            'zip' => ['icon' => 'fa-file-archive', 'class' => 'zip'],
            'rar' => ['icon' => 'fa-file-archive', 'class' => 'zip'],
            '7z' => ['icon' => 'fa-file-archive', 'class' => 'zip'],
            'txt' => ['icon' => 'fa-file-alt', 'class' => 'text'],
            'csv' => ['icon' => 'fa-file-csv', 'class' => 'text'],
            'json' => ['icon' => 'fa-file-code', 'class' => 'code'],
            'xml' => ['icon' => 'fa-file-code', 'class' => 'code'],
            'html' => ['icon' => 'fa-file-code', 'class' => 'code'],
            'css' => ['icon' => 'fa-file-code', 'class' => 'code'],
            'js' => ['icon' => 'fa-file-code', 'class' => 'code'],
            'php' => ['icon' => 'fa-file-code', 'class' => 'code'],
        ];
        
        return $icons[$ext] ?? ['icon' => 'fa-file', 'class' => 'default'];
    }

    /**
     * Get file type category
     */
    public function getFileCategory()
    {
        $ext = strtolower($this->getFileExtension());
        
        $categories = [
            'pdf' => 'Document',
            'doc' => 'Document',
            'docx' => 'Document',
            'xls' => 'Spreadsheet',
            'xlsx' => 'Spreadsheet',
            'ppt' => 'Presentation',
            'pptx' => 'Presentation',
            'jpg' => 'Image',
            'jpeg' => 'Image',
            'png' => 'Image',
            'gif' => 'Image',
            'svg' => 'Image',
            'zip' => 'Archive',
            'rar' => 'Archive',
            '7z' => 'Archive',
            'txt' => 'Text',
            'csv' => 'Data',
            'json' => 'Code',
            'xml' => 'Code',
            'html' => 'Code',
            'css' => 'Code',
            'js' => 'Code',
            'php' => 'Code',
        ];
        
        return $categories[$ext] ?? 'Other';
    }

    // ============================================
    // REVIEW METHODS
    // ============================================

    /**
     * Check if the submission is reviewable
     */
    public function isReviewable()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the submission can be edited/deleted
     */
    public function isEditable()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Get the time since submission
     */
    public function getSubmittedAgo()
    {
        return $this->submitted_at ? $this->submitted_at->diffForHumans() : null;
    }

    /**
     * Get the time since review
     */
    public function getReviewedAgo()
    {
        return $this->reviewed_at ? $this->reviewed_at->diffForHumans() : null;
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeRevisionRequested($query)
    {
        return $query->where('status', self::STATUS_REVISION_REQUESTED);
    }

    public function scopeReviewed($query)
    {
        return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_REVISION_REQUESTED]);
    }

    public function scopeSubmittedBy($query, $userId)
    {
        return $query->where('submitted_by', $userId);
    }

    public function scopeForTask($query, $taskId)
    {
        return $query->where('task_id', $taskId);
    }

    // ============================================
    // ATTRIBUTE ACCESSORS
    // ============================================

    /**
     * Get the full file URL
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/task_submissions/' . $this->file_path);
    }

    /**
     * Get the file size in KB
     */
    public function getFileSizeKbAttribute()
    {
        return round($this->file_size / 1024, 2);
    }

    /**
     * Get the file size in MB
     */
    public function getFileSizeMbAttribute()
    {
        return round($this->file_size / 1048576, 2);
    }

    /**
     * Check if the submission has comments
     */
    public function getHasCommentsAttribute()
    {
        return $this->comments()->count() > 0;
    }

    /**
     * Get the comment count
     */
    public function getCommentCountAttribute()
    {
        return $this->comments()->count();
    }

    // ============================================
    // BOOT METHOD
    // ============================================

    protected static function boot()
    {
        parent::boot();

        // Auto-set submitted_at when creating
        static::creating(function ($submission) {
            if (empty($submission->submitted_at)) {
                $submission->submitted_at = now();
            }
            if (empty($submission->status)) {
                $submission->status = self::STATUS_PENDING;
            }
        });
    }
}