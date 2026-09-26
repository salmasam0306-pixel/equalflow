<?php
// app/Services/WorkloadAnalyzer.php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class WorkloadAnalyzer
{
    /**
     * Max active tasks a member can hold before being "overloaded".
     */
    const MAX_CAPACITY = 10;

    /**
     * Analyze workload for a collection of users.
     * Returns a normalized array that both the Blade view and the AI service can consume.
     */
    public function analyze($users): array
    {
        $results = [];

        foreach ($users as $user) {
            $pending = Task::where('assigned_to', $user->id)
                ->whereIn('status', ['todo', 'in_progress', 'review'])
                ->count();

            $completed = Task::where('assigned_to', $user->id)
                ->where('status', 'done')
                ->count();

            $percentage = min((int) round(($pending / self::MAX_CAPACITY) * 100), 100);

            $results[] = [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'profile_picture'   => $user->getProfilePictureUrl(),

                'job_scope'         => $user->getJobScopeLabel(),
                'job_scope_icon'    => $user->getJobScopeIcon(),
                'job_scope_raw'     => $user->job_scope,

                'status'            => $user->status,
                'status_label'      => $user->getStatusLabel(),
                'status_icon'       => $user->getStatusIcon(),

                'pending_tasks'     => $pending,
                'completed_tasks'   => $completed,
                'total_tasks'       => $pending + $completed,

                'workload_percent'  => $percentage,
                'workload_color'    => $this->getWorkloadColor($percentage),
                'workload_label'    => $this->getWorkloadLabel($percentage),

                'availability'      => $this->getAvailability($user),

                // Skills — MUST match what AIService uses
                'specialties'       => $user->specialties ?? [],
                'all_skills'        => $user->getAllSkills(),

                'experience_level'  => $user->experience_level ?? null,
            ];
        }

        return $results;
    }

    /**
     * Score users for a specific task (local heuristic).
     * Used as a fallback when the Python AI service is offline.
     */
    public function scoreForTask(array $analyzedUsers, array $taskSkills): array
    {
        $weights = [
            'skill_match'        => 0.60,
            'workload_score'     => 0.25,
            'performance_score'  => 0.10,
            'availability_score' => 0.05,
        ];

        foreach ($analyzedUsers as &$u) {
            $matchedSkills = [];
            $missingSkills = [];

            // Prefer all_skills (specialties + job scope).
            // Fall back to specialties for safety.
            $memberSkills = $u['all_skills'] ?? $u['specialties'] ?? [];

            if (!empty($taskSkills)) {
                foreach ($taskSkills as $skill) {
                    if (in_array($skill, $memberSkills, true)) {
                        $matchedSkills[] = $skill;
                    } else {
                        $missingSkills[] = $skill;
                    }
                }
                $skillScore = (count($matchedSkills) / max(count($taskSkills), 1)) * 100;
            } else {
                $skillScore = 80;
            }

            $workloadScore = max(0, 100 - ($u['workload_percent'] ?? 0));

            $total = $u['total_tasks'] ?? 0;
            $done  = $u['completed_tasks'] ?? 0;
            $performanceScore = $total > 0 ? min(($done / $total) * 100, 100) : 70;

            $availabilityScore = match ($u['availability'] ?? 'unknown') {
                'available' => 100,
                'away'      => 50,
                'on_leave'  => 10,
                default     => 60,
            };

            $weightedTotal =
                $skillScore        * $weights['skill_match'] +
                $workloadScore     * $weights['workload_score'] +
                $performanceScore  * $weights['performance_score'] +
                $availabilityScore * $weights['availability_score'];

            $u['matched_skills'] = $matchedSkills;
            $u['missing_skills'] = $missingSkills;
            $u['ai_score']       = round($weightedTotal, 1);
            $u['ai_source']      = 'local';
            $u['ai_breakdown']   = [
                'skill_match'        => round($skillScore, 1),
                'workload_score'     => round($workloadScore, 1),
                'performance_score'  => round($performanceScore, 1),
                'availability_score' => round($availabilityScore, 1),
            ];
            $u['ai_weighting']   = $weights;
        }
        unset($u);

        usort($analyzedUsers, fn ($a, $b) => $b['ai_score'] <=> $a['ai_score']);

        if (!empty($analyzedUsers)) {
            $analyzedUsers[0]['is_recommended'] = true;
        }

        return $analyzedUsers;
    }

    private function getWorkloadColor(int $percent): string
    {
        if ($percent <= 40) return 'green';
        if ($percent <= 70) return 'yellow';
        if ($percent <= 90) return 'orange';
        return 'red';
    }

    private function getWorkloadLabel(int $percent): string
    {
        if ($percent <= 40) return 'Available';
        if ($percent <= 70) return 'Moderate';
        if ($percent <= 90) return 'Busy';
        return 'Overloaded';
    }

    private function getAvailability(User $user): string
    {
        return match ($user->status) {
            'active'        => 'available',
            'outstation'    => 'away',
            'annual_leave',
            'medical_leave' => 'on_leave',
            default         => 'unknown',
        };
    }
}