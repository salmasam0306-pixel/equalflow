<?php
// app/Services/AIService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;

class AIService
{
    protected string $apiUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl  = config('services.ai.api_url', 'http://localhost:8000');
        $this->timeout = (int) config('services.ai.timeout', 30);
    }

    // =========================================================
    // HEALTH
    // =========================================================

    public function isRunning(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            Log::warning('AI service is not running: ' . $e->getMessage());
            return false;
        }
    }

    // =========================================================
    // TASK MATCHING
    // =========================================================

    public function matchTask($task, $candidates): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/match', [
                    'task'       => $this->formatTask($task),
                    'candidates' => $this->formatCandidates($candidates),
                ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error('AI Match failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return $this->fallbackMatch($task, $candidates);
        } catch (\Exception $e) {
            Log::error('AI Match error: ' . $e->getMessage());
            return $this->fallbackMatch($task, $candidates);
        }
    }

    public function scoreCandidates(array $taskContext, array $candidates): array
    {
        if (empty($candidates)) {
            return [];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/match', [
                    'task' => [
                        'task_id'          => $taskContext['task_id'] ?? 0,
                        'title'            => $taskContext['title'] ?? 'Untitled task',
                        'description'      => $taskContext['description'] ?? null,
                        'skills_required'  => $taskContext['skills_required'] ?? [],
                        'experience_level' => $taskContext['experience_level'] ?? null,
                        'estimated_hours'  => $taskContext['estimated_hours'] ?? null,
                    ],
                    'candidates' => collect($candidates)->map(function ($c) {
                        return [
                            'user_id'          => $c['id'] ?? null,
                            'name'             => $c['name'] ?? 'Unknown',
                            'skills'           => $c['all_skills'] ?? $c['specialties'] ?? [],
                            'job_scope'        => $c['job_scope_raw'] ?? null,
                            'experience_level' => $c['experience_level'] ?? null,
                            'completed_tasks'  => $c['completed_tasks'] ?? 0,
                            'total_tasks'      => $c['total_tasks'] ?? 0,
                            'is_on_leave'      => ($c['availability'] ?? null) === 'on_leave',
                            'is_outstation'    => ($c['availability'] ?? null) === 'away',
                        ];
                    })->values()->all(),
                ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::warning('AI scoreCandidates non-success', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return [];
        } catch (\Exception $e) {
            Log::info('AI scoreCandidates unavailable: ' . $e->getMessage());
            return [];
        }
    }

    public function getTaskRecommendations(Task $task, $project): array
    {
        $members = $this->getProjectMembers($project);

        if ($members->isEmpty()) {
            return [];
        }

        return $this->matchTask($task, $members);
    }

    // =========================================================
    // WORKLOAD
    // =========================================================

    public function analyzeWorkload($users): array
    {
        try {
            $formattedUsers = $users->map(function ($user) {
                $activeTasks = Task::where('assigned_to', $user->id)
                    ->whereIn('status', ['todo', 'in_progress', 'review'])
                    ->count();

                return [
                    'user_id'      => $user->id,
                    'active_tasks' => $activeTasks,
                ];
            })->values()->toArray();

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/workload/analyze', $formattedUsers);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Workload analysis error: ' . $e->getMessage());
            return [];
        }
    }

    public function rebalanceTeam($users): array
    {
        try {
            $formattedUsers = $users->map(function ($user) {
                $activeTasks = Task::where('assigned_to', $user->id)
                    ->whereIn('status', ['todo', 'in_progress', 'review'])
                    ->count();

                return [
                    'user_id'      => $user->id,
                    'active_tasks' => $activeTasks,
                ];
            })->values()->toArray();

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/team/rebalance', $formattedUsers);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Team rebalance error: ' . $e->getMessage());
            return [];
        }
    }

    // =========================================================
    // PROJECT HEALTH
    // =========================================================

    public function analyzeProjectHealth($project): array
    {
        try {
            $totalTasks     = $project->tasks()->count();
            $completedTasks = $project->tasks()->where('status', 'done')->count();
            $overdueTasks   = $project->tasks()
                ->where('due_date', '<', now())
                ->where('status', '!=', 'done')
                ->count();
            $blockedTasks   = $project->tasks()->where('is_blocked', true)->count();

            $response = Http::timeout($this->timeout)
                ->post($this->apiUrl . '/api/project/health', [
                    'project_id'      => $project->id,
                    'total_tasks'     => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'overdue_tasks'   => $overdueTasks,
                    'blocked_tasks'   => $blockedTasks,
                ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Project health analysis error: ' . $e->getMessage());
            return [];
        }
    }

    // =========================================================
    // FORMATTERS
    // =========================================================

    protected function formatTask($task): array
    {
        return [
            'task_id'          => $task->id,
            'title'            => $task->title,
            'description'      => $task->description,
            'skills_required'  => $task->skills_required ?? [],
            'experience_level' => $task->experience_level,
            'estimated_hours'  => $task->estimated_hours,
        ];
    }

    protected function formatCandidates($candidates): array
    {
        return $candidates->map(function ($user) {
            return [
                'user_id'          => $user->id,
                'name'             => $user->name,
                'skills'           => $this->getUserSkills($user),
                'job_scope'        => $user->job_scope,
                'experience_level' => $user->experience_level ?? null,
                'completed_tasks'  => $this->getCompletedTasks($user),
                'total_tasks'      => $this->getTotalTasks($user),
                'is_on_leave'      => method_exists($user, 'isOnLeave') ? $user->isOnLeave() : false,
                'is_outstation'    => method_exists($user, 'isOutstation') ? $user->isOutstation() : false,
            ];
        })->values()->toArray();
    }

    /**
     * Get the skills a user "has" for AI matching purposes.
     * Delegates to User::getAllSkills() so all services agree.
     */
    protected function getUserSkills($user): array
    {
        if (method_exists($user, 'getAllSkills')) {
            return $user->getAllSkills();
        }

        // Fallback: at minimum use specialties
        return is_array($user->specialties ?? null) ? $user->specialties : [];
    }

    protected function getProjectMembers($project): Collection
    {
        return $project->members()
            ->where('users.role', '!=', 'principal')
            ->get();
    }

    protected function getCompletedTasks($user): int
    {
        return Task::where('assigned_to', $user->id)
            ->where('status', 'done')
            ->count();
    }

    protected function getTotalTasks($user): int
    {
        return Task::where('assigned_to', $user->id)->count();
    }

    // =========================================================
    // FALLBACK (when AI service is down)
    // =========================================================

    protected function fallbackMatch($task, $candidates): array
    {
        $results = [];

        foreach ($candidates as $candidate) {
            $score = $this->calculateFallbackScore($task, $candidate);

            $results[] = [
                'user_id'         => $candidate->id,
                'user_name'       => $candidate->name,
                'score'           => $score,
                'level'           => $this->getFallbackLevel($score),
                'recommendation'  => "Fallback match for {$candidate->name}",
                'skills_match'    => $this->getMatchingSkills($candidate, $task->skills_required ?? []),
                'skills_missing'  => $this->getMissingSkills($candidate, $task->skills_required ?? []),
                'is_fallback'     => true,
            ];
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }

    protected function calculateFallbackScore($task, $candidate): float
    {
        $score = 50;

        $taskSkills = $task->skills_required ?? [];
        $userSkills = $this->getUserSkills($candidate);

        if (!empty($taskSkills) && !empty($userSkills)) {
            $matched = count(array_intersect($taskSkills, $userSkills));
            $score += ($matched / max(count($taskSkills), 1)) * 30;
        }

        $completedTasks = $this->getCompletedTasks($candidate);
        $score += min($completedTasks / 5, 20);

        return min($score, 100);
    }

    protected function getFallbackLevel($score): string
    {
        if ($score >= 85) return 'excellent';
        if ($score >= 70) return 'good';
        if ($score >= 50) return 'fair';
        return 'poor';
    }

    protected function getMatchingSkills($user, array $taskSkills): array
    {
        $userSkills = $this->getUserSkills($user);
        return array_values(array_intersect($taskSkills, $userSkills));
    }

    protected function getMissingSkills($user, array $taskSkills): array
    {
        $userSkills = $this->getUserSkills($user);
        return array_values(array_diff($taskSkills, $userSkills));
    }
}