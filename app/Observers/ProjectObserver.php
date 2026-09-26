<?php
// app/Observers/ProjectObserver.php

namespace App\Observers;

use App\Models\Project;
use App\Models\ProjectMember;

class ProjectObserver
{
    public function saved(Project $project): void
    {
        if (!$project->leader_id) {
            return;
        }

        ProjectMember::updateOrCreate(
            [
                'project_id' => $project->id,
                'user_id'    => $project->leader_id,
            ],
            [
                'role'      => 'leader',
                'status'    => 'active',
                'joined_at' => now(),
            ]
        );
    }
}