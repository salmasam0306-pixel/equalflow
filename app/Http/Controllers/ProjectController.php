<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Models\OrganizationMember;
use App\Helpers\NotificationHelper;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $user = auth()->user();

        if ($user->isPrincipal()) {
            $org = $user->currentOrganization();
            $projects = Project::where('organization_id', $org?->id)
                ->where('type', 'company')
                ->with(['tasks', 'creator'])
                ->orderByDesc('created_at')
                ->get();
        } elseif ($user->isLeader()) {
            // Leaders see projects that have at least one main task assigned to them.
            $projects = Project::where('organization_id', $user->currentOrganization()?->id)
                ->where('type', 'company')
                ->whereHas('mainTasks', function ($q) use ($user) {
                    $q->where('assigned_to', $user->id);
                })
                ->with(['tasks', 'creator'])
                ->orderByDesc('created_at')
                ->get();
        } elseif ($user->isMember()) {
            // Members see projects they have a sub-task in.
            $projects = Project::where('organization_id', $user->currentOrganization()?->id)
                ->where('type', 'company')
                ->whereHas('tasks', function ($q) use ($user) {
                    $q->where('assigned_to', $user->id);
                })
                ->with(['tasks', 'creator'])
                ->orderByDesc('created_at')
                ->get();
        } else {
            $projects = collect();
        }

        return view('projects.index', compact('projects'));
    }

    // =========================================================
    // CREATE (Principal only)
    // =========================================================
    public function create()
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can create projects.');
        }

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'You need to create or join a company first.');
        }

        return view('projects.create');
    }

    // =========================================================
    // STORE (Principal only)
    // =========================================================
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can create projects.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'nullable|date',
            'deadline'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'You need to create or join a company first.');
        }

        $project = Project::create([
            'name'            => $validated['name'],
            'description'     => $validated['description'] ?? null,
            'organization_id' => $org->id,
            'created_by'      => $user->id,
            'leader_id'       => null,       // legacy column, unused
            'type'            => 'company',
            'status'          => 'active',
            'start_date'      => $validated['start_date'] ?? null,
            'deadline'        => $validated['deadline'] ?? null,
        ]);

        NotificationHelper::projectCreated($project);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created. Add a main task and assign a leader.');
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show(Project $project)
    {
        $this->authorizeAccess($project);

        $project->load([
            'tasks' => fn ($q) => $q->with(['assignee', 'creator', 'department']),
            'creator',
        ]);

        return view('projects.show', compact('project'));
    }

    // =========================================================
    // EDIT (Principal only)
    // =========================================================
    public function edit(Project $project)
    {
        $this->authorizeAccess($project);

        $user = auth()->user();
        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can edit projects.');
        }

        return view('projects.edit', compact('project'));
    }

    // =========================================================
    // UPDATE (Principal only)
    // =========================================================
    public function update(Request $request, Project $project)
    {
        $this->authorizeAccess($project);

        $user = auth()->user();
        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can update projects.');
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,completed,on_hold',
            'start_date'  => 'nullable|date',
            'deadline'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $oldStatus = $project->status;

        $project->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
            'start_date'  => $validated['start_date'] ?? null,
            'deadline'    => $validated['deadline'] ?? null,
        ]);

        // Notify all project members when the project becomes completed.
        if ($oldStatus !== $project->status && $project->status === 'completed') {
            foreach ($project->activeMembers as $member) {
                NotificationHelper::systemNotification(
                    $member->id,
                    "Project Completed: {$project->name} 🎉",
                    "Project '{$project->name}' has been marked as completed.",
                    route('projects.show', $project)
                );
            }
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    // =========================================================
    // DESTROY (Principal only)
    // =========================================================
    public function destroy(Project $project)
    {
        $this->authorizeAccess($project);

        $user = auth()->user();
        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can delete projects.');
        }

        foreach ($project->activeMembers as $member) {
            NotificationHelper::systemNotification(
                $member->id,
                "Project Deleted: {$project->name}",
                "The project '{$project->name}' has been deleted.",
                route('projects.index')
            );
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

        // =========================================================
    // REPORT
    // =========================================================

    /**
     * Show the project report (read-only summary with printable layout).
     */
    public function report(Project $project)
    {
        $this->authorizeAccess($project);

        $user = auth()->user();

        $isPrincipal = $user->isPrincipal();
        $isLeader = $project->tasks()
            ->where('assigned_to', $user->id)
            ->where('task_type', 'main')
            ->exists();
        $isCompleted = $project->status === 'completed';

        // Members can only view once the project is completed.
        // Principal and leaders can view any time.
        if (!$isPrincipal && !$isLeader && !$isCompleted) {
            abort(403, 'The project report is only available after the project is completed.');
        }

        $project->load([
            'organization',
            'creator',
            'documents.uploader',
            'tasks' => fn ($q) => $q->with([
                'assignee',
                'creator',
                'department',
                'subtasks' => fn ($qq) => $qq->with([
                    'assignee',
                    'department',
                    'submissions' => fn ($qqq) => $qqq->with(['submitter', 'reviewer']),
                ]),
            ]),
        ]);

        $mainTasks = $project->tasks->where('task_type', 'main');
        $subTasks  = $project->tasks->where('task_type', 'sub');

        // ---- Stats ----
        $stats = [
            'total_main_tasks'      => $mainTasks->count(),
            'completed_main_tasks'  => $mainTasks->where('status', 'done')->count(),
            'total_sub_tasks'       => $subTasks->count(),
            'completed_sub_tasks'   => $subTasks->where('status', 'done')->count(),
            'total_submissions'     => $subTasks->sum(fn ($t) => $t->submissions->count()),
            'approved_submissions'  => $subTasks->sum(fn ($t) => $t->submissions->where('status', 'approved')->count()),
            'documents_count'       => $project->documents->count(),
        ];

        // ---- Per-department breakdown ----
        $deptStats = $mainTasks
            ->groupBy('department_id')
            ->map(function ($tasks, $deptId) {
                $dept = $tasks->first()->department;
                return [
                    'name'  => $dept?->name ?? 'Standalone',
                    'total' => $tasks->count(),
                    'done'  => $tasks->where('status', 'done')->count(),
                ];
            })
            ->values();

        // ---- Contributors ----
        $contributors = collect()
            ->merge($subTasks->pluck('assignee')->filter())
            ->merge($mainTasks->pluck('assignee')->filter())
            ->unique('id')
            ->values();

        // ---- Timeline (approved + rejected submissions) ----
        $timeline = $subTasks
            ->flatMap(fn ($t) => $t->submissions->map(fn ($s) => [
                'date'        => $s->submitted_at,
                'task'        => $t->title,
                'user'        => $s->submitter,
                'status'      => $s->status,
                'reviewed_by' => $s->reviewer,
                'reviewed_at' => $s->reviewed_at,
            ]))
            ->sortBy('date')
            ->values();

        return view('projects.report', compact(
            'project',
            'stats',
            'deptStats',
            'contributors',
            'timeline',
            'mainTasks',
            'subTasks',
            'isPrincipal',
            'isLeader',
            'isCompleted'
        ));
    }

    // =========================================================
    // PROJECT DOCUMENTS
    // =========================================================

    /**
     * Upload a project document (Principal only).
     */
    public function uploadDocument(Request $request, Project $project)
    {
        $this->authorizeAccess($project);

        if (!auth()->user()->isPrincipal()) {
            abort(403, 'Only the Principal can upload project documents.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file'        => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,7z,txt,csv,dwg,dxf',
        ]);

        $file = $request->file('file');
        $filename = time()
            . '_' . $project->id
            . '_' . uniqid()
            . '.' . strtolower($file->getClientOriginalExtension());

        $file->storeAs('project_documents', $filename, 'public');

        ProjectDocument::create([
            'project_id'  => $project->id,
            'uploaded_by' => auth()->id(),
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $filename,
            'file_type'   => $file->getMimeType(),
            'file_size'   => $file->getSize(),
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    /**
     * Delete a project document (Principal only).
     */
    public function deleteDocument(Project $project, ProjectDocument $document)
    {
        $this->authorizeAccess($project);

        if (!auth()->user()->isPrincipal()) {
            abort(403, 'Only the Principal can delete project documents.');
        }

        if ($document->project_id !== $project->id) {
            abort(404);
        }

        \Illuminate\Support\Facades\Storage::disk('public')
            ->delete('project_documents/' . $document->file_path);

        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    /**
     * Download a project document.
     */
    public function downloadDocument(Project $project, ProjectDocument $document)
    {
        $this->authorizeAccess($project);

        if ($document->project_id !== $project->id) {
            abort(404);
        }

        $path = storage_path('app/public/project_documents/' . $document->file_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File not found.');
        }

        return response()->download($path, $document->file_name);
    }

    // =========================================================
    // LEGACY (retired — redirects, kept so old links don't 404)
    // =========================================================
    public function assignLeader(Project $project)
    {
        return redirect()->route('projects.show', $project)
            ->with('info', 'Project leaders are now assigned per main task.');
    }

    public function storeLeader(Request $request, Project $project)
    {
        return redirect()->route('projects.show', $project)
            ->with('info', 'Project leaders are now assigned per main task.');
    }

    // =========================================================
    // HELPERS
    // =========================================================
    private function authorizeAccess(Project $project)
    {
        $user = auth()->user();

        if ($project->isPersonal()) {
            if ($project->created_by !== $user->id) {
                abort(403);
            }
            return;
        }

        if (!$project->organization_id) {
            abort(403);
        }

        $isMember = OrganizationMember::where('organization_id', $project->organization_id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$isMember) {
            abort(403);
        }
    }
}