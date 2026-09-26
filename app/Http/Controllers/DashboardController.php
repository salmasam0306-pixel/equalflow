<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\OrganizationMember;
use App\Services\AIService;
use App\Helpers\AIHelper;
use Illuminate\Http\Request;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $user = auth()->user();
        $org = $user->currentOrganization();
        $data = [];
        $user->checkAndUpdateStatus();

        // Check AI status
        $aiStatus = $this->aiService->isRunning();

        switch ($user->role) {
            case 'principal':
                if ($org) {
                    $data = [
                        'total_projects' => Project::where('organization_id', $org->id)->count(),
                        'total_members' => OrganizationMember::where('organization_id', $org->id)->count(),
                        'active_projects' => Project::where('organization_id', $org->id)
                            ->where('status', 'active')->count(),
                        'completed_projects' => Project::where('organization_id', $org->id)
                            ->where('status', 'completed')->count(),
                        'projects' => Project::where('organization_id', $org->id)
                            ->with(['leader', 'tasks'])
                            ->orderBy('created_at', 'desc')
                            ->get(),
                        'members' => OrganizationMember::where('organization_id', $org->id)
                            ->with('user')
                            ->get(),
                    ];

                    // Get AI Project Health Insights
                    if ($aiStatus && !empty($data['projects'])) {
                        $projectHealth = [];
                        foreach ($data['projects'] as $project) {
                            $health = $this->aiService->analyzeProjectHealth($project);
                            if (!empty($health)) {
                                $projectHealth[] = [
                                    'project' => $project,
                                    'health' => $health
                                ];
                            }
                        }
                        $data['project_health'] = $projectHealth;
                    }

                    // Get AI Workload Analysis
                    if ($aiStatus && !empty($data['members'])) {
                        $members = collect();
                        foreach ($data['members'] as $member) {
                            $members->push($member->user);
                        }
                        $workload = $this->aiService->analyzeWorkload($members);
                        if (!empty($workload)) {
                            $data['team_workload'] = $workload;
                        }
                    }
                } else {
                    $data = [];
                }
                break;

            case 'leader':
                $projects = Project::where('leader_id', $user->id)->get();
                $projectIds = $projects->pluck('id');
                
                $data = [
                    'projects' => $projects,
                    'total_tasks' => Task::whereIn('project_id', $projectIds)->count(),
                    'completed_tasks' => Task::whereIn('project_id', $projectIds)
                        ->where('status', 'done')->count(),
                    'pending_tasks' => Task::whereIn('project_id', $projectIds)
                        ->whereIn('status', ['todo', 'in_progress', 'review'])->count(),
                    'tasks' => Task::whereIn('project_id', $projectIds)
                        ->with(['project', 'assignee'])
                        ->orderBy('created_at', 'desc')
                        ->get(),
                ];

                // Get AI Project Health for leader's projects
                if ($aiStatus && !empty($data['projects'])) {
                    $projectHealth = [];
                    foreach ($data['projects'] as $project) {
                        $health = $this->aiService->analyzeProjectHealth($project);
                        if (!empty($health)) {
                            $projectHealth[] = [
                                'project' => $project,
                                'health' => $health
                            ];
                        }
                    }
                    $data['project_health'] = $projectHealth;
                }
                break;

            case 'member':
                $data = [
                    'assigned_tasks' => Task::where('assigned_to', $user->id)
                        ->where('status', '!=', 'done')
                        ->with('project')
                        ->orderBy('created_at', 'desc')
                        ->get(),
                    'completed_tasks' => Task::where('assigned_to', $user->id)
                        ->where('status', 'done')
                        ->with('project')
                        ->orderBy('created_at', 'desc')
                        ->get(),
                    'total_tasks' => Task::where('assigned_to', $user->id)->count(),
                ];

                // Get AI Workload Analysis for member
                if ($aiStatus) {
                    $workload = $this->aiService->analyzeWorkload(collect([$user]));
                    if (!empty($workload)) {
                        $data['workload_analysis'] = $workload[0] ?? null;
                    }
                }
                break;

            case 'personal':
                $data = [
                    'personal_projects' => Project::where('created_by', $user->id)
                        ->where('type', 'personal')
                        ->withCount('tasks')
                        ->orderBy('created_at', 'desc')
                        ->get(),
                    'total_projects' => Project::where('created_by', $user->id)
                        ->where('type', 'personal')->count(),
                    'total_tasks' => Task::whereHas('project', function($q) use ($user) {
                        $q->where('created_by', $user->id)
                          ->where('type', 'personal');
                    })->count(),
                ];

                // Get AI Project Health for personal projects
                if ($aiStatus && !empty($data['personal_projects'])) {
                    $projectHealth = [];
                    foreach ($data['personal_projects'] as $project) {
                        $health = $this->aiService->analyzeProjectHealth($project);
                        if (!empty($health)) {
                            $projectHealth[] = [
                                'project' => $project,
                                'health' => $health
                            ];
                        }
                    }
                    $data['project_health'] = $projectHealth;
                }
                break;

            default:
                $data = [
                    'projects' => collect([]),
                    'total_tasks' => 0,
                    'completed_tasks' => 0,
                    'pending_tasks' => 0,
                    'tasks' => collect([]),
                ];
                break;
        }

        // Add AI status to view data
        $data['ai_status'] = $aiStatus ? 'connected' : 'disconnected';

        return view('dashboard', compact('data', 'user', 'org'));
    }
}