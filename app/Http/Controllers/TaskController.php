<?php
// app/Http/Controllers/TaskController.php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Department;
use App\Models\User;
use App\Models\ProjectMember;
use App\Models\TaskDependency;
use App\Models\TaskSubmission;
use App\Models\TaskSubmissionComment;
use App\Services\AIService;
use App\Services\WorkloadAnalyzer;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    protected AIService $aiService;
    protected WorkloadAnalyzer $workload;

    public function __construct(AIService $aiService, WorkloadAnalyzer $workload)
    {
        $this->aiService = $aiService;
        $this->workload  = $workload;
    }

    // =========================================================
    // MAIN TASK — create (Principal only)
    // =========================================================
    public function create(Project $project)
    {
        $user = Auth::user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can create main tasks.');
        }

        $org = $user->currentOrganization();
        if (!$org || $project->organization_id !== $org->id) {
            abort(403);
        }

        $departments = Department::where('organization_id', $org->id)
            ->where('status', 'active')
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $siblingMainTasks = Task::where('project_id', $project->id)
            ->where('task_type', Task::TYPE_MAIN)
            ->whereNull('parent_task_id')
            ->orderBy('title')
            ->get();

        return view('tasks.create', compact('project', 'departments', 'siblingMainTasks'));
    }

    // =========================================================
    // MAIN TASK — store
    //
    // Handles both company AND personal projects:
    //   - Company project  → only the Principal can create main tasks
    //   - Personal project → only the project creator can create tasks
    // =========================================================
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'project_id'        => 'required|exists:projects,id',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'department_id'     => 'nullable|exists:departments,id',
            'assigned_to'       => 'required|exists:users,id',
            'priority'          => 'required|in:low,medium,high',
            'start_date'        => 'nullable|date',
            'due_date'          => 'required|date|after_or_equal:start_date',
            'estimated_hours'   => 'nullable|integer|min:1',
            'skills_required'   => 'nullable|array',
            'skills_required.*' => 'string|max:255',
            'dependencies'      => 'nullable|array',
            'dependencies.*'    => 'exists:tasks,id',
        ]);

        $project = Project::findOrFail($validated['project_id']);
        $isPersonal = $project->type === 'personal';

        // ===== AUTHORIZATION =====
        if ($isPersonal) {
            if ((int) $project->created_by !== (int) $user->id) {
                abort(403, 'Only the project creator can add tasks to a personal project.');
            }
        } else {
            if (!$user->isPrincipal()) {
                abort(403, 'Only the Principal can create main tasks.');
            }

            $org = $user->currentOrganization();
            if (!$org || $project->organization_id !== $org->id) {
                abort(403, 'You do not have access to this project.');
            }
        }

        $assignee = User::findOrFail($validated['assigned_to']);

        // ✅ PERSONAL PROJECT MEMBERSHIP: auto-add assignee so the project
        // shows up in their personal projects list even without an invite.
        if ($isPersonal) {
            $exists = ProjectMember::where('project_id', $project->id)
                ->where('user_id', $assignee->id)
                ->exists();

            if (!$exists) {
                ProjectMember::create([
                    'project_id' => $project->id,
                    'user_id'    => $assignee->id,
                ]);
            }
        }

        // ===== COMPANY-ONLY VALIDATIONS =====
        if (!$isPersonal) {
            if ($assignee->isPrincipal()) {
                return back()->withErrors(['assigned_to' => 'Cannot assign a task to the Principal.'])->withInput();
            }

            if (!$assignee->isEngineer()) {
                return back()->withErrors([
                    'assigned_to' => 'Only engineers can be assigned as a main task leader.'
                ])->withInput();
            }

            if (!empty($validated['department_id'])) {
                if ((int) $assignee->department_id !== (int) $validated['department_id']) {
                    return back()
                        ->withErrors(['assigned_to' => 'The selected leader does not belong to the chosen department.'])
                        ->withInput();
                }
            }
        }

        // ---- Enforce: start_date must not precede (latest blocking deadline + 1 day) ----
        if (!empty($validated['dependencies'])) {
            $blockers = Task::whereIn('id', $validated['dependencies'])
                ->where('status', '!=', Task::STATUS_DONE)
                ->get();

            if ($blockers->isNotEmpty()) {
                $latestDue = $blockers->pluck('due_date')->filter()->max();

                if ($latestDue && !empty($validated['start_date'])) {
                    $minStart    = $latestDue->copy()->addDay();
                    $chosenStart = \Carbon\Carbon::parse($validated['start_date']);

                    if ($chosenStart->lt($minStart)) {
                        return back()
                            ->withErrors([
                                'start_date' => "Start date must be on or after {$minStart->format('d M Y')} — because the blocking task(s) aren't done yet."
                            ])
                            ->withInput();
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            $task = Task::create([
                'parent_task_id'   => null,
                'project_id'       => $project->id,
                'department_id'    => $isPersonal ? null : ($validated['department_id'] ?: null),
                'task_type'        => Task::TYPE_MAIN,
                'assigned_by'      => $user->id,
                'assigned_to'      => $assignee->id,
                'created_by'       => $user->id,
                'title'            => $validated['title'],
                'description'      => $validated['description'] ?? null,
                'status'           => Task::STATUS_TODO,
                'priority'         => $validated['priority'],
                'start_date'       => $validated['start_date'] ?? null,
                'due_date'         => $validated['due_date'],
                'estimated_hours'  => $validated['estimated_hours'] ?? null,
                'skills_required'  => $validated['skills_required'] ?? null,
                'is_assigned'      => true,
                'assigned_at'      => now(),
                'is_blocked'       => false,
            ]);

            if (!empty($validated['dependencies'])) {
                foreach ($validated['dependencies'] as $depTaskId) {
                    if ((int) $depTaskId === (int) $task->id) continue;

                    TaskDependency::create([
                        'task_id'            => $task->id,
                        'depends_on_task_id' => $depTaskId,
                        'dependency_type'    => 'finish_to_start',
                    ]);
                }

                $task->refresh();
                $task->is_blocked = $task->isBlocked();
                $task->save();
            }

            DB::commit();
            NotificationHelper::taskAssigned($task);

            if ($isPersonal) {
                return redirect()
                    ->route('personal.show', $project)
                    ->with('success', 'Task created and assigned to ' . $assignee->name . '.')
                    ->with('notify_refresh', true);
            }

            return redirect()
                ->route('tasks.show', $task)
                ->with('success', 'Main task created and assigned to ' . $assignee->name . '.')
                ->with('notify_refresh', true);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create task: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================
    // MAIN TASK — show
    // =========================================================
    public function show(Task $task)
    {
        $user = Auth::user();

        if (!$task->isMain()) {
            return redirect()->route('tasks.subtask.show', $task);
        }

        if (!$task->isVisibleTo($user)) {
            abort(403, 'You do not have access to this task.');
        }

        $task->load([
            'project',
            'department',
            'assignee',
            'assigner',
            'subtasks' => fn ($q) => $q->with(['assignee', 'department'])->orderBy('created_at'),
            'dependsOnTasks',
        ]);

        return view('tasks.show', compact('task'));
    }

    // =========================================================
    // SUB-TASK — edit
    // =========================================================
    public function editSubtask(Task $task)
    {
        if (!$task->isSub()) abort(404);

        $user = Auth::user();
        $parent = $task->parent;

        $isLeader = $parent && $parent->assigned_to === $user->id;
        $isAssignee = $task->assigned_to === $user->id;

        if (!$isLeader && !$isAssignee) {
            abort(403, 'Only the sub-task leader or assignee can edit.');
        }

        $org = $user->currentOrganization();
        $departments = Department::where('organization_id', $org->id)
            ->where('status', 'active')
            ->withCount('members')
            ->orderBy('name')
            ->get();

        $siblings = $parent
            ? $parent->subtasks()->where('id', '!=', $task->id)->orderBy('title')->get()
            : collect();

        $currentDependencies = $task->dependsOnTasks->pluck('id')->toArray();

        return view('tasks.subtask-edit', compact(
            'task', 'parent', 'departments', 'siblings', 'currentDependencies'
        ));
    }

    // =========================================================
    // SUB-TASK — update
    // =========================================================
    public function updateSubtask(Request $request, Task $task)
    {
        if (!$task->isSub()) abort(404);

        $user = Auth::user();
        $parent = $task->parent;

        $isLeader = $parent && $parent->assigned_to === $user->id;
        $isAssignee = $task->assigned_to === $user->id;

        if (!$isLeader && !$isAssignee) abort(403);

        $canReassign = $isLeader;
        $canReschedule = $isLeader;

        $rules = [
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'priority'          => 'required|in:low,medium,high',
            'status'            => 'required|in:todo,in_progress,review,done,blocked',
            'skills_required'   => 'nullable|array',
            'skills_required.*' => 'string|max:255',
            'dependencies'      => 'nullable|array',
            'dependencies.*'    => 'exists:tasks,id',
        ];

        if ($canReassign) {
            $rules['assigned_to']   = 'nullable|exists:users,id';
            $rules['department_id'] = 'nullable|exists:departments,id';
        }

        if ($canReschedule) {
            $rules['start_date']      = 'nullable|date';
            $rules['due_date']        = 'nullable|date|after_or_equal:start_date';
            $rules['estimated_hours'] = 'nullable|integer|min:1';
        }

        $validated = $request->validate($rules);

        $oldAssignee = $task->assigned_to;

        $payload = [
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'priority'         => $validated['priority'],
            'status'           => $validated['status'],
            'skills_required'  => $validated['skills_required'] ?? null,
        ];

        if ($canReassign) {
            $payload['assigned_to']   = $validated['assigned_to'] ?? null;
            $payload['department_id'] = $validated['department_id'] ?: null;

            if (!empty($validated['assigned_to'])) {
                $assignee = User::find($validated['assigned_to']);
                if ($assignee && $assignee->isPrincipal()) {
                    return back()->withErrors([
                        'assigned_to' => 'Cannot assign a sub-task to the Principal.'
                    ])->withInput();
                }
                if (!empty($validated['department_id'])
                    && (int) $assignee->department_id !== (int) $validated['department_id']) {
                    return back()->withErrors([
                        'assigned_to' => 'The selected member does not belong to the chosen department.'
                    ])->withInput();
                }
            }
        }

        if ($canReschedule) {
            $payload['start_date']      = $validated['start_date'] ?? null;
            $payload['due_date']        = $validated['due_date'] ?? null;
            $payload['estimated_hours'] = $validated['estimated_hours'] ?? null;
        }

        if ($validated['status'] === Task::STATUS_DONE) {
            $payload['completed_at'] = now();
        }

        $task->update($payload);

        // ✅ PERSONAL PROJECT MEMBERSHIP: if this sub-task belongs to a
        // personal project and got (re)assigned, ensure the new assignee
        // is a member so the project appears on their dashboard.
        if ($canReassign
            && !empty($validated['assigned_to'])
            && $task->project
            && $task->project->type === 'personal') {

            $exists = ProjectMember::where('project_id', $task->project_id)
                ->where('user_id', $validated['assigned_to'])
                ->exists();

            if (!$exists) {
                ProjectMember::create([
                    'project_id' => $task->project_id,
                    'user_id'    => $validated['assigned_to'],
                ]);
            }
        }

        $newAssignee = $task->assigned_to;

        if ($oldAssignee && $newAssignee && (int) $oldAssignee !== (int) $newAssignee) {
            NotificationHelper::taskReassigned($task, (int) $oldAssignee, (int) $newAssignee);
        } elseif ($oldAssignee && !$newAssignee) {
            NotificationHelper::taskUnassigned($task, (int) $oldAssignee);
        } elseif (!$oldAssignee && $newAssignee) {
            NotificationHelper::taskAssigned($task);
        }

        if ($request->has('dependencies')) {
            if (!empty($validated['dependencies'])) {
                $blockers = Task::whereIn('id', $validated['dependencies'])
                    ->where('id', '!=', $task->id)
                    ->where('status', '!=', Task::STATUS_DONE)
                    ->get();

                if ($blockers->isNotEmpty()) {
                    $latestDue = $blockers->pluck('due_date')->filter()->max();

                    if ($latestDue && !empty($validated['start_date'])) {
                        $minStart = $latestDue->copy()->addDay();
                        $chosenStart = \Carbon\Carbon::parse($validated['start_date']);

                        if ($chosenStart->lt($minStart)) {
                            return back()
                                ->withErrors([
                                    'start_date' => "Start date must be on or after {$minStart->format('d M Y')}."
                                ])
                                ->withInput();
                        }
                    }
                }
            }
            TaskDependency::where('task_id', $task->id)->delete();
            foreach ($request->dependencies as $depTaskId) {
                if ($depTaskId != $task->id) {
                    TaskDependency::create([
                        'task_id'            => $task->id,
                        'depends_on_task_id' => $depTaskId,
                        'dependency_type'    => 'finish_to_start',
                    ]);
                }
            }
            $task->refresh();
            $task->is_blocked = $task->isBlocked();
            $task->save();
        }

        return redirect()->route('tasks.subtask.show', $task)
            ->with('success', 'Sub-task updated.')
            ->with('notify_refresh', true);
    }

    // =========================================================
    // SUB-TASK — delete
    // =========================================================
    public function destroySubtask(Task $task)
    {
        if (!$task->isSub()) abort(404);

        $user = Auth::user();
        $parent = $task->parent;

        if (!$parent || $parent->assigned_to !== $user->id) {
            abort(403, 'Only the assigned leader can delete sub-tasks.');
        }

        TaskDependency::where('task_id', $task->id)->delete();
        TaskDependency::where('depends_on_task_id', $task->id)->delete();

        $parentId = $task->parent_task_id;
        $title = $task->title;
        $task->delete();

        NotificationHelper::systemNotification(
            Auth::id(),
            'You deleted a sub-task: ' . $title,
            'It has been removed from the parent task.',
            route('tasks.show', $parentId)
        );

        return redirect()->route('tasks.show', $parentId)
            ->with('success', 'Sub-task deleted.')
            ->with('notify_refresh', true);
    }

    // =========================================================
    // AJAX: department members + AI workload
    // =========================================================
    public function getDepartmentMembersWithWorkload(Department $department, Request $request)
    {
        $user = Auth::user();
        $org  = $user->currentOrganization();

        if (!$org || $department->organization_id !== $org->id) {
            abort(403);
        }

        $taskSkills = $request->get('skills', []);
        if (is_string($taskSkills)) $taskSkills = [$taskSkills];

        $engineersOnly = $request->boolean('engineers_only');

        $membersQuery = $department->members();
        if ($engineersOnly) {
            $membersQuery = $membersQuery->engineers();
        }
        $members = $membersQuery->get();

        $analyzed = $this->workload->analyze($members);
        $scored   = $this->workload->scoreForTask($analyzed, $taskSkills);

        $aiRanking = $this->aiService->scoreCandidates(
            [
                'task_id'          => 0,
                'title'            => 'Task for ' . $department->name,
                'skills_required'  => $taskSkills,
                'experience_level' => null,
            ],
            $analyzed
        );

        $aiSource = 'local';

        if (!empty($aiRanking)) {
            $aiSource = 'python';

            $aiByUser = collect($aiRanking)->keyBy('user_id');

            $aiWeights = [
                'skill_match'        => 0.35,
                'experience_match'   => 0.20,
                'workload_score'     => 0.20,
                'performance_score'  => 0.15,
                'availability_score' => 0.10,
            ];

            $scored = collect($scored)->map(function ($row) use ($aiByUser, $aiWeights) {
                $ai = $aiByUser->get($row['id']);

                if ($ai) {
                    $row['ai_score']          = $ai['score'];
                    $row['ai_level']          = $ai['level'] ?? null;
                    $row['ai_source']         = 'python';
                    $row['ai_recommendation'] = $ai['recommendation'] ?? null;

                    if (!empty($ai['skills_match'])) {
                        $row['matched_skills'] = $ai['skills_match'];
                    }
                    if (!empty($ai['skills_missing'])) {
                        $row['missing_skills'] = $ai['skills_missing'];
                    }

                    $row['ai_breakdown'] = $ai['breakdown'] ?? null;
                    $row['ai_weighting'] = $aiWeights;
                }

                return $row;
            })
            ->sortByDesc(fn ($r) => $r['ai_score'] ?? 0)
            ->values()
            ->all();

            foreach ($scored as &$row) {
                $row['is_recommended'] = false;
            }
            unset($row);

            if (!empty($scored)) {
                $scored[0]['is_recommended'] = true;
            }
        }

        return response()->json([
            'success'    => true,
            'department' => ['id' => $department->id, 'name' => $department->name],
            'members'    => $scored,
            'ai_source'  => $aiSource,
        ]);
    }

    // =========================================================
    // SUB-TASK — create (Leader only)
    // =========================================================
    public function createSubtask(Task $parentTask)
    {
        if (!$parentTask->isMain()) abort(404);

        $user = Auth::user();

        if ($parentTask->assigned_to !== $user->id) {
            abort(403, 'Only the assigned leader can add sub-tasks.');
        }

        $org = $user->currentOrganization();
        $departments = Department::where('organization_id', $org->id)
            ->where('status', 'active')
            ->withCount('members')
            ->orderBy('name')
            ->get();

        return view('tasks.subtask-create', compact('parentTask', 'departments'));
    }

    // =========================================================
    // SUB-TASK — store
    // =========================================================
    public function storeSubtask(Request $request, Task $parentTask)
    {
        if (!$parentTask->isMain()) abort(404);

        $user = Auth::user();

        if ($parentTask->assigned_to !== $user->id) {
            abort(403, 'Only the assigned leader can add sub-tasks.');
        }

        $validated = $request->validate([
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'department_id'     => 'nullable|exists:departments,id',
            'assigned_to'       => 'nullable|exists:users,id',
            'priority'          => 'required|in:low,medium,high',
            'start_date'        => 'nullable|date',
            'due_date'          => 'required|date|after_or_equal:start_date',
            'estimated_hours'   => 'nullable|integer|min:1',
            'skills_required'   => 'nullable|array',
            'skills_required.*' => 'string|max:255',
        ]);

        if (!empty($validated['assigned_to'])) {
            $assignee = User::findOrFail($validated['assigned_to']);
            if ($assignee->isPrincipal()) {
                return back()->withErrors(['assigned_to' => 'Cannot assign a sub-task to the Principal.'])->withInput();
            }
            if (!empty($validated['department_id'])
                && (int) $assignee->department_id !== (int) $validated['department_id']) {
                return back()
                    ->withErrors(['assigned_to' => 'The selected member does not belong to the chosen department.'])
                    ->withInput();
            }
        }

        // ✅ PERSONAL PROJECT MEMBERSHIP: auto-add sub-task assignee so
        // the parent project appears on their dashboard.
        if (!empty($validated['assigned_to'])
            && $parentTask->project
            && $parentTask->project->type === 'personal') {

            $exists = ProjectMember::where('project_id', $parentTask->project_id)
                ->where('user_id', $validated['assigned_to'])
                ->exists();

            if (!$exists) {
                ProjectMember::create([
                    'project_id' => $parentTask->project_id,
                    'user_id'    => $validated['assigned_to'],
                ]);
            }
        }

        DB::beginTransaction();
        try {
            $subtask = Task::create([
                'parent_task_id'   => $parentTask->id,
                'project_id'       => $parentTask->project_id,
                'department_id'    => $validated['department_id'] ?: null,
                'task_type'        => Task::TYPE_SUB,
                'assigned_by'      => $user->id,
                'assigned_to'      => $validated['assigned_to'] ?? null,
                'created_by'       => $user->id,
                'title'            => $validated['title'],
                'description'      => $validated['description'] ?? null,
                'status'           => Task::STATUS_TODO,
                'priority'         => $validated['priority'],
                'start_date'       => $validated['start_date'] ?? null,
                'due_date'         => $validated['due_date'],
                'estimated_hours'  => $validated['estimated_hours'] ?? null,
                'skills_required'  => $validated['skills_required'] ?? null,
                'is_assigned'      => !empty($validated['assigned_to']),
                'assigned_at'      => !empty($validated['assigned_to']) ? now() : null,
                'is_blocked'       => false,
            ]);

            DB::commit();

            if ($subtask->assigned_to) {
                NotificationHelper::taskAssigned($subtask);
            }

            return redirect()
                ->route('tasks.show', $parentTask)
                ->with('success', 'Sub-task created' . ($subtask->assigned_to ? ' and assigned.' : '.'))
                ->with('notify_refresh', true);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create sub-task: ' . $e->getMessage())->withInput();
        }
    }

    // =========================================================
    // SUB-TASK — show
    // =========================================================
    public function showSubtask(Task $task)
    {
        if (!$task->isSub()) abort(404);

        $user = Auth::user();

        if (!$task->isVisibleTo($user)) {
            abort(403, 'You do not have access to this sub-task.');
        }

        $task->load([
            'project',
            'department',
            'assignee',
            'assigner',
            'parent',
            'submissions.submitter',
            'submissions.reviewer',
            'submissions.comments.user',
        ]);

        return view('tasks.subtask-show', compact('task'));
    }

    // =========================================================
    // STATUS — quick change
    // =========================================================
    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => 'required|in:todo,in_progress,review,done,blocked']);

        $user = Auth::user();
        $parent = $task->parent;
        $project = $task->project;
        $isPersonal = $project && $project->type === 'personal';

        $isAssignee = $task->assigned_to === $user->id;
        $isParentLeader = $parent && $parent->assigned_to === $user->id;
        $isCreator = $isPersonal && (int) $project->created_by === (int) $user->id;

        if (!$isAssignee && !$isParentLeader && !$isCreator) {
            abort(403, 'You do not have permission to update this task.');
        }

        if ($request->status === 'done' && $isAssignee && !$isParentLeader && !$isCreator) {
            $task->status = Task::STATUS_REVIEW;
            $task->save();
            NotificationHelper::taskSubmitted($task, null);
            return redirect()->back()
                ->with('success', 'Task marked for review.')
                ->with('notify_refresh', true);
        }

        $task->status = $request->status;
        if ($request->status === Task::STATUS_DONE) {
            $task->completed_at = now();
        }
        $task->save();

        if ($request->status === Task::STATUS_DONE && !$isPersonal) {
            $this->checkAndUnblockDependentTasks($task);
        }

        return redirect()->back()
            ->with('success', 'Task status updated.')
            ->with('notify_refresh', true);
    }

    private function checkAndUnblockDependentTasks(Task $task)
    {
        $dependentTasks = Task::whereHas('dependsOnTasks', function ($query) use ($task) {
            $query->where('depends_on_task_id', $task->id);
        })->get();

        foreach ($dependentTasks as $dependentTask) {
            if (!$dependentTask->isBlocked()) {
                $dependentTask->is_blocked = false;
                $dependentTask->save();
            }
        }
    }

    // =========================================================
    // MAIN TASK — edit / update / destroy
    // =========================================================
    public function edit(Task $task)
    {
        $user = Auth::user();

        if (!$task->isMain()) {
            return redirect()->route('tasks.subtask.show', $task);
        }

        $isPrincipal = $user->isPrincipal();
        $isLeader = $task->assigned_to === $user->id;

        if (!$isPrincipal && !$isLeader) {
            abort(403, 'Only the Principal or the assigned leader can edit this task.');
        }

        $org = $user->currentOrganization();
        $departments = Department::where('organization_id', $org->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $siblingMainTasks = Task::where('project_id', $task->project_id)
            ->where('task_type', Task::TYPE_MAIN)
            ->whereNull('parent_task_id')
            ->where('id', '!=', $task->id)
            ->orderBy('title')
            ->get();

        $currentDependencies = $task->dependsOnTasks->pluck('id')->toArray();

        return view('tasks.edit', compact('task', 'departments', 'siblingMainTasks', 'currentDependencies'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        $project = $task->project;
        $isPersonal = $project && $project->type === 'personal';

        // ===== AUTHORIZATION =====
        if ($isPersonal) {
            if ((int) $task->created_by !== (int) $user->id) {
                abort(403, 'Only the task creator can edit this task.');
            }
        } else {
            $isPrincipal = $user->isPrincipal();
            $isLeader = $task->assigned_to === $user->id;
            if (!$isPrincipal && !$isLeader) abort(403);
        }

        $rules = [
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'priority'          => 'required|in:low,medium,high',
            'start_date'        => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:start_date',
            'estimated_hours'   => 'nullable|integer|min:1',
            'status'            => 'required|in:todo,in_progress,review,done,blocked',
        ];

        if (!$isPersonal) {
            $rules['department_id']     = 'nullable|exists:departments,id';
            $rules['assigned_to']       = 'nullable|exists:users,id';
            $rules['skills_required']   = 'nullable|array';
            $rules['skills_required.*'] = 'string|max:255';
            $rules['dependencies']      = 'nullable|array';
            $rules['dependencies.*']    = 'exists:tasks,id';
        } else {
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $validated = $request->validate($rules);

        if (!$isPersonal && !empty($validated['assigned_to'])) {
            $newLeader = User::find($validated['assigned_to']);
            if ($newLeader && !$newLeader->isEngineer() && $task->isMain()) {
                return back()->withErrors([
                    'assigned_to' => 'Only engineers can be assigned as a main task leader.'
                ])->withInput();
            }
        }

        if (!$isPersonal && !empty($validated['dependencies'])) {
            $blockers = Task::whereIn('id', $validated['dependencies'])
                ->where('id', '!=', $task->id)
                ->where('status', '!=', Task::STATUS_DONE)
                ->get();

            if ($blockers->isNotEmpty()) {
                $latestDue = $blockers->pluck('due_date')->filter()->max();
                if ($latestDue && !empty($validated['start_date'])) {
                    $minStart = $latestDue->copy()->addDay();
                    $chosenStart = \Carbon\Carbon::parse($validated['start_date']);
                    if ($chosenStart->lt($minStart)) {
                        return back()->withErrors([
                            'start_date' => "Start date must be on or after {$minStart->format('d M Y')}."
                        ])->withInput();
                    }
                }
            }
        }

        $oldAssignee = $task->assigned_to;

        DB::beginTransaction();
        try {
            $payload = [
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'priority'    => $validated['priority'],
                'start_date'  => $validated['start_date'] ?? null,
                'due_date'    => $validated['due_date'] ?? null,
                'status'      => $validated['status'],
            ];

            if (!$isPersonal) {
                $payload['department_id']   = $validated['department_id'] ?: null;
                $payload['assigned_to']     = $validated['assigned_to'] ?? null;
                $payload['estimated_hours'] = $validated['estimated_hours'] ?? null;
                $payload['skills_required'] = $validated['skills_required'] ?? null;
            } else {
                $payload['assigned_to']     = $validated['assigned_to'] ?? null;
                $payload['estimated_hours'] = $validated['estimated_hours'] ?? null;
            }

            if ($validated['status'] === Task::STATUS_DONE) {
                $payload['completed_at'] = now();
            }

            $task->update($payload);

            // ✅ PERSONAL PROJECT MEMBERSHIP: if this is a personal project
            // and the task was (re)assigned, ensure the new assignee is a
            // member so the project shows up on their dashboard.
            if ($isPersonal
                && !empty($validated['assigned_to'])
                && $project) {

                $exists = ProjectMember::where('project_id', $project->id)
                    ->where('user_id', $validated['assigned_to'])
                    ->exists();

                if (!$exists) {
                    ProjectMember::create([
                        'project_id' => $project->id,
                        'user_id'    => $validated['assigned_to'],
                    ]);
                }
            }

            if (!$isPersonal) {
                TaskDependency::where('task_id', $task->id)->delete();
                if ($request->has('dependencies') && is_array($request->dependencies)) {
                    foreach ($request->dependencies as $depTaskId) {
                        if ((int) $depTaskId !== (int) $task->id) {
                            TaskDependency::create([
                                'task_id'            => $task->id,
                                'depends_on_task_id' => $depTaskId,
                                'dependency_type'    => 'finish_to_start',
                            ]);
                        }
                    }
                }
                $task->refresh();
                $task->is_blocked = $task->isBlocked();
                $task->save();
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update task: ' . $e->getMessage())->withInput();
        }

        $newAssignee = $task->assigned_to;

        if ($oldAssignee && $newAssignee && (int) $oldAssignee !== (int) $newAssignee) {
            NotificationHelper::taskReassigned($task, (int) $oldAssignee, (int) $newAssignee);
        } elseif ($oldAssignee && !$newAssignee) {
            NotificationHelper::taskUnassigned($task, (int) $oldAssignee);
        } elseif (!$oldAssignee && $newAssignee) {
            NotificationHelper::taskAssigned($task);
        }

        $redirect = $isPersonal
            ? redirect()->route('personal.show', $project)->with('success', 'Task updated.')
            : redirect()->route('tasks.show', $task)->with('success', 'Task updated.');

        return $redirect->with('notify_refresh', true);
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();
        $project = $task->project;

        if ($project && $project->type === 'personal') {
            if ((int) $project->created_by !== (int) $user->id) {
                abort(403, 'Only the project creator can delete tasks in a personal project.');
            }
        } else {
            if (!$user->isPrincipal()) {
                abort(403, 'Only the Principal can delete tasks.');
            }
        }

        if ($task->isMain()) {
            $assignees = $task->subtasks()->whereNotNull('assigned_to')->pluck('assigned_to')->unique();
            foreach ($assignees as $userId) {
                NotificationHelper::systemNotification(
                    $userId,
                    'Main Task Deleted: ' . $task->title,
                    'A main task you were working on has been deleted.',
                    route('projects.show', $task->project_id)
                );
            }
        }

        TaskDependency::where('task_id', $task->id)->delete();
        TaskDependency::where('depends_on_task_id', $task->id)->delete();

        $projectId = $task->project_id;
        $task->delete();

        if ($project && $project->type === 'personal') {
            return redirect()->route('personal.show', $projectId)
                ->with('success', 'Task deleted.')
                ->with('notify_refresh', true);
        }

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Task deleted.')
            ->with('notify_refresh', true);
    }

    // =========================================================
    // DEPENDENCIES (AJAX)
    // =========================================================
    public function getDependencyStatus(Task $task)
    {
        $dependencies = $task->dependsOnTasks()->select('tasks.id', 'tasks.title', 'tasks.status')->get();
        $blockingTasks = $task->getBlockingTasks();

        return response()->json([
            'dependencies'    => $dependencies,
            'blocking_count'  => $blockingTasks->count(),
            'is_blocked'      => $task->is_blocked,
            'can_complete'    => $blockingTasks->count() === 0,
            'progress'        => $task->getDependencyProgress(),
        ]);
    }

    public function smartDatePreview(Request $request)
    {
        $request->validate([
            'dependencies'   => 'required|array',
            'dependencies.*' => 'exists:tasks,id',
        ]);

        $dependencies = Task::whereIn('id', $request->dependencies)->get();

        $latestDueDate = null;
        $allDone = true;

        foreach ($dependencies as $dep) {
            if ($dep->status !== 'done') $allDone = false;
            if ($dep->due_date) {
                if (!$latestDueDate || $dep->due_date > $latestDueDate) {
                    $latestDueDate = $dep->due_date;
                }
            }
        }

        $recommendedStartDate = $latestDueDate ? $latestDueDate->copy()->addDay() : null;

        return response()->json([
            'latest_due_date'        => $latestDueDate?->format('Y-m-d'),
            'recommended_start_date' => $recommendedStartDate?->format('Y-m-d'),
            'all_done'               => $allDone,
            'blocking_count'         => $dependencies->where('status', '!=', 'done')->count(),
        ]);
    }

    // =========================================================
    // SUBMISSIONS — form (legacy route)
    // =========================================================
    public function submitForm(Task $task)
    {
        if (!$task->isSub()) {
            return redirect()->route('tasks.show', $task);
        }

        return redirect()->route('tasks.subtask.show', $task);
    }

    // =========================================================
    // SUBMISSIONS — upload
    // =========================================================
    public function uploadSubmission(Request $request, Task $task)
    {
        $user = Auth::user();
        $project = $task->project;
        $isPersonal = $project && $project->type === 'personal';

        if (!$isPersonal && !$task->isSub()) {
            abort(403, 'Only sub-tasks can be submitted.');
        }

        if ((int) $task->assigned_to !== (int) $user->id) {
            abort(403, 'Only the assignee can submit work.');
        }

        $validated = $request->validate([
            'files'       => 'required|array|min:1|max:10',
            'files.*'     => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,zip,rar,7z,txt,csv,dwg,dxf',
            'description' => 'nullable|string|max:1000',
        ]);

        if (!$isPersonal && $task->dependsOnTasks()->count() > 0) {
            $blocking = $task->getBlockingTasks();
            if ($blocking->count() > 0) {
                return back()->with('error', 'Cannot submit. Depends on: ' . $blocking->pluck('title')->join(', '));
            }
        }

        $newVersion = ((int) $task->submissions()->max('version')) + 1;

        $submissions = [];

        DB::beginTransaction();
        try {
            foreach ($request->file('files') as $file) {
                $filename = time()
                    . '_' . $task->id
                    . '_v' . $newVersion
                    . '_' . uniqid()
                    . '.' . strtolower($file->getClientOriginalExtension());

                $file->storeAs('task_submissions', $filename, 'public');

                $submissions[] = TaskSubmission::create([
                    'task_id'       => $task->id,
                    'submitted_by'  => $user->id,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_path'     => $filename,
                    'file_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                    'description'   => $validated['description'] ?? null,
                    'status'        => TaskSubmission::STATUS_PENDING,
                    'submitted_at'  => now(),
                    'version'       => $newVersion,
                ]);
            }

            $task->status = Task::STATUS_REVIEW;
            $task->save();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ($submissions as $s) {
                Storage::disk('public')->delete('task_submissions/' . $s->file_path);
            }

            return back()->with('error', 'Failed to submit: ' . $e->getMessage());
        }

        NotificationHelper::taskSubmitted($task, $submissions[0] ?? null);

        $redirect = $isPersonal
            ? redirect()->route('personal.show', $project)->with('success', 'Submission uploaded for review.')
            : redirect()->route('tasks.subtask.show', $task)->with('success', 'Submission uploaded for review.');

        return $redirect->with('notify_refresh', true);
    }

    // =========================================================
    // SUBMISSIONS — comment
    // =========================================================
    public function addComment(Request $request, TaskSubmission $submission)
    {
        $user = Auth::user();
        $task = $submission->task;
        $parent = $task->parent;
        $project = $task->project;
        $isPersonal = $project && $project->type === 'personal';

        $isAssignee = $task->assigned_to === $user->id;
        $isLeader = $parent && $parent->assigned_to === $user->id;
        $isPrincipal = $user->isPrincipal();
        $isCreator = $isPersonal && (int) $project->created_by === (int) $user->id;

        if (!$isAssignee && !$isLeader && !$isPrincipal && !$isCreator) abort(403);

        $request->validate(['comment' => 'required|string|max:1000']);

        $comment = TaskSubmissionComment::create([
            'submission_id' => $submission->id,
            'user_id'       => $user->id,
            'comment'       => $request->comment,
        ]);

        NotificationHelper::commentAdded($comment, $submission);

        $redirect = $isPersonal
            ? redirect()->route('personal.show', $project)
            : redirect()->route('tasks.subtask.show', $task);

        return $redirect->with('success', 'Comment added.')->with('notify_refresh', true);
    }

    // =========================================================
    // SUBMISSIONS — review
    // =========================================================
    public function reviewSubmission(Request $request, TaskSubmission $submission)
    {
        $user = Auth::user();
        $task = $submission->task;
        $parent = $task->parent;
        $project = $task->project;
        $isPersonal = $project && $project->type === 'personal';

        // ===== AUTHORIZATION =====
        if ($isPersonal) {
            // Only the project creator can review
            if ((int) $project->created_by !== (int) $user->id) {
                abort(403, 'Only the project creator can review submissions.');
            }
            // Self-review IS allowed for personal projects (solo workflows).
        } else {
            $isLeader = $parent && $parent->assigned_to === $user->id;
            $isPrincipal = $user->isPrincipal();
            if (!$isLeader && !$isPrincipal) {
                abort(403, 'Only the main-task leader or Principal can review.');
            }
            // Company projects block self-review (second pair of eyes)
            if ((int) $submission->submitted_by === (int) $user->id) {
                return back()->with('error', 'You cannot review your own submission.');
            }
        }

        if ($submission->status !== TaskSubmission::STATUS_PENDING) {
            return back()->with('error', 'This submission has already been reviewed.');
        }

        $request->validate([
            'status'       => 'required|in:approved,rejected,revision_requested',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $submission->update([
                'status'        => $request->status,
                'review_notes'  => $request->review_notes,
                'reviewed_by'   => $user->id,
                'reviewed_at'   => now(),
            ]);

            switch ($request->status) {
                case 'approved':
                    $task->status = Task::STATUS_DONE;
                    $task->completed_at = now();
                    $task->save();
                    if (!$isPersonal) {
                        $this->checkAndUnblockDependentTasks($task);
                    }
                    break;

                case 'revision_requested':
                    $task->status = Task::STATUS_IN_PROGRESS;
                    $task->completed_at = null;
                    $task->save();
                    break;

                case 'rejected':
                    $task->status = Task::STATUS_TODO;
                    $task->completed_at = null;
                    $task->save();
                    break;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save review: ' . $e->getMessage());
        }

        NotificationHelper::taskReviewed($task, $submission, $request->status);

        $flashes = [
            'approved'           => ['success', 'Submission approved.'],
            'revision_requested' => ['warning', 'Revision requested.'],
            'rejected'           => ['error', 'Submission rejected.'],
        ];
        [$type, $msg] = $flashes[$request->status];

        $redirect = $isPersonal
            ? redirect()->route('personal.show', $project)
            : redirect()->route('tasks.subtask.show', $task);

        return $redirect->with($type, $msg)->with('notify_refresh', true);
    }

    // =========================================================
    // SUBMISSIONS — download
    // =========================================================
    public function downloadSubmission(TaskSubmission $submission)
    {
        $user = Auth::user();
        $task = $submission->task;

        if (!$task->isVisibleTo($user)) {
            abort(403);
        }

        $filePath = storage_path('app/public/task_submissions/' . $submission->file_path);
        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($filePath, $submission->file_name);
    }

    // =========================================================
    // SUBMISSIONS — delete
    // =========================================================
    public function deleteSubmission(TaskSubmission $submission)
    {
        $user = Auth::user();
        $task = $submission->task;
        $parent = $task->parent;
        $project = $task->project;
        $isPersonal = $project && $project->type === 'personal';

        if ($isPersonal) {
            $isSubmitter = (int) $submission->submitted_by === (int) $user->id;
            $isCreator = (int) $project->created_by === (int) $user->id;
            if (!$isSubmitter && !$isCreator) {
                abort(403, 'You cannot delete this submission.');
            }
        } else {
            $isSubmitter = (int) $submission->submitted_by === (int) $user->id;
            $isLeader = $parent && $parent->assigned_to === $user->id;
            $isPrincipal = $user->isPrincipal();
            if (!$isSubmitter && !$isLeader && !$isPrincipal) abort(403);
        }

        if ($submission->status !== TaskSubmission::STATUS_PENDING) {
            return back()->with('error', 'Cannot delete a submission that has been reviewed.');
        }

        Storage::disk('public')->delete('task_submissions/' . $submission->file_path);
        $submission->delete();

        if (!$task->submissions()->where('status', TaskSubmission::STATUS_PENDING)->exists()) {
            $task->status = Task::STATUS_TODO;
            $task->save();
        }

        $redirect = $isPersonal
            ? redirect()->route('personal.show', $project)
            : redirect()->route('tasks.subtask.show', $task);

        return $redirect->with('success', 'Submission deleted.')->with('notify_refresh', true);
    }

    // =========================================================
    // DEPRECATED (redirects)
    // =========================================================
    public function showAssign(Task $task)
    {
        return redirect()->route('tasks.show', $task);
    }

    public function assignMember(Request $request, Task $task)
    {
        return redirect()->route('tasks.show', $task);
    }
}