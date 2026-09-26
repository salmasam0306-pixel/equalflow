<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $fillable = ['project_id', 'user_id', 'role', 'status', 'joined_at'];

    const ROLE_LEADER = 'leader';
    const ROLE_MEMBER = 'member';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}