<?php
// app/Models/Department.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasHashid;

class Department extends Model
{

    use HasHashid;

    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_members')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    // ============================================
    // SCOPES
    // ============================================
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // ============================================
    // HELPERS
    // ============================================
    public function getMemberCount(): int
    {
        return $this->members()->count();
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function addMember(User $user): bool
    {
        if ($this->isMember($user)) {
            return false;
        }

        $this->members()->attach($user->id, [
            'joined_at' => now(),
        ]);

        $user->update(['department_id' => $this->id]);

        return true;
    }

    public function removeMember(User $user): bool
    {
        if (!$this->isMember($user)) {
            return false;
        }

        $this->members()->detach($user->id);

        if ($user->department_id === $this->id) {
            $user->update(['department_id' => null]);
        }

        return true;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canDelete(): bool
    {
        return $this->members()->count() === 0;
    }
}