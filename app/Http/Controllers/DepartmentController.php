<?php
// app/Http/Controllers/DepartmentController.php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use App\Models\Task;
use App\Helpers\NotificationHelper;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    protected AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $user = Auth::user();
        $org = $user->currentOrganization();

        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'Please create or join a company first.');
        }

        $departments = Department::where('organization_id', $org->id)
            ->with('members')
            ->get();

        $unassignedMembers = User::whereHas('organizationMembers', function ($query) use ($org) {
                $query->where('organization_id', $org->id)
                      ->where('role', '!=', 'principal');
            })
            ->where('role', '!=', 'principal')
            ->whereNull('department_id')
            ->get();

        $members = User::whereHas('organizationMembers', function ($query) use ($org) {
            $query->where('organization_id', $org->id);
        })->with('department')->get();

        return view('departments.index', compact(
            'departments',
            'unassignedMembers',
            'members',
            'org'
        ));
    }

    // =========================================================
    // SHOW
    // =========================================================
    public function show(Department $department)
    {
        $this->authorizeAccess($department);

        $department->load(['members']);

        return view('departments.show', compact('department'));
    }

    // =========================================================
    // CREATE (Principal only)
    // =========================================================
    public function create()
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can create departments.');
        }

        $org = Auth::user()->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'Please create a company first.');
        }

        $availableMembers = User::whereHas('organizationMembers', function ($query) use ($org) {
                $query->where('organization_id', $org->id)
                      ->where('role', '!=', 'principal');
            })
            ->where('role', '!=', 'principal')
            ->whereNull('department_id')
            ->get();

        return view('departments.create', compact('availableMembers', 'org'));
    }

    // =========================================================
    // STORE (Principal only)
    // =========================================================
    public function store(Request $request)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can create departments.');
        }

        $org = Auth::user()->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'Please create a company first.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'members'     => 'nullable|array',
            'members.*'   => 'exists:users,id',
        ]);

        $department = Department::create([
            'organization_id' => $org->id,
            'name'            => $request->name,
            'description'     => $request->description,
            'created_by'      => Auth::id(),
            'status'          => 'active',
        ]);

        if ($request->has('members')) {
            foreach ($request->members as $memberId) {
                $member = User::find($memberId);
                if ($member && !$member->isPrincipal()) {
                    $department->addMember($member);
                    NotificationHelper::memberAddedToDepartment($member, $department);
                }
            }
        }

        return redirect()->route('departments.show', $department)
            ->with('success', 'Department created successfully!')
            ->with('notify_refresh', true);
    }

    // =========================================================
    // EDIT (Principal only)
    // =========================================================
    public function edit(Department $department)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can edit departments.');
        }

        $this->authorizeAccess($department);

        return view('departments.edit', compact('department'));
    }

    // =========================================================
    // UPDATE (Principal only)
    // =========================================================
    public function update(Request $request, Department $department)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can update departments.');
        }

        $this->authorizeAccess($department);

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,inactive',
        ]);

        $department->update([
            'name'        => $request->name,
            'description' => $request->description,
            'status'      => $request->status ?? 'active',
        ]);

        return redirect()->route('departments.show', $department)
            ->with('success', 'Department updated successfully!');
    }

    // =========================================================
    // DESTROY (Principal only)
    // =========================================================
    public function destroy(Department $department)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can delete departments.');
        }

        $this->authorizeAccess($department);

        if ($department->members()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete department with members. Please remove all members first.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    // =========================================================
    // ADD MEMBER (Principal only) — quick add (department only)
    // =========================================================
    public function addMember(Request $request, Department $department)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can manage members.');
        }

        $this->authorizeAccess($department);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $member = User::find($request->user_id);

        if ($member->isPrincipal()) {
            return redirect()->back()->with('error', 'The Principal cannot be added to a department.');
        }

        if ($department->isMember($member)) {
            return redirect()->back()->with('error', 'User is already a member of this department.');
        }

        $department->addMember($member);
        NotificationHelper::memberAddedToDepartment($member, $department);

        return redirect()->route('departments.show', $department)
            ->with('success', 'Member added successfully!')
            ->with('notify_refresh', true);
    }

    // =========================================================
    // REMOVE MEMBER (Principal only)
    // =========================================================
    public function removeMember(Department $department, User $user)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can manage members.');
        }

        $this->authorizeAccess($department);

        NotificationHelper::memberRemovedFromDepartment($user, $department);

        $department->removeMember($user);

        return redirect()->route('departments.show', $department)
            ->with('success', 'Member removed successfully!')
            ->with('notify_refresh', true);
    }

    // =========================================================
    // ASSIGN DEPARTMENT FORM (Principal only)
    //
    // When opened from inside a department (?from=department&department_id=X):
    //   - the department is pre-selected on the form
    //   - Cancel / after-save returns to the department page
    // =========================================================
    public function assignDepartment(Request $request, User $user)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can assign departments.');
        }

        if ($user->isPrincipal()) {
            return redirect()->route('departments.index')
                ->with('error', 'The Principal cannot be assigned to a department.');
        }

        $org = Auth::user()->currentOrganization();
        $departments = Department::where('organization_id', $org->id)
            ->withCount('members')
            ->orderBy('name')
            ->get();

        // Context: was the form opened from inside a department?
        $fromDepartment = $request->get('from') === 'department';
        $contextDepartmentId = $request->get('department_id');

        return view('departments.assign-department', compact(
            'user',
            'departments',
            'fromDepartment',
            'contextDepartmentId'
        ));
    }

    // =========================================================
    // UPDATE USER'S DEPARTMENT + JOB SCOPE + SPECIALTIES
    // =========================================================
    public function updateDepartment(Request $request, User $user)
    {
        if (!Auth::user()->isPrincipal()) {
            abort(403, 'Only the Principal can assign departments.');
        }

        if ($user->isPrincipal()) {
            return redirect()->route('departments.index')
                ->with('error', 'The Principal cannot be assigned to a department.');
        }

        $validated = $request->validate([
            'department_id' => 'required|exists:departments,id',
            'job_scope'     => 'nullable|string|max:255',
            'specialties'   => 'nullable|array|max:10',
            'specialties.*' => 'string|max:255',
            'from'          => 'nullable|string',
        ]);

        $department = Department::findOrFail($validated['department_id']);

        $org = Auth::user()->currentOrganization();
        if (!$org || $department->organization_id !== $org->id) {
            abort(403, 'Invalid department.');
        }

        $oldDepartmentId = $user->department_id;

        // Remove from old department if different
        if ($oldDepartmentId && $oldDepartmentId !== $department->id) {
            $oldDepartment = Department::find($oldDepartmentId);
            if ($oldDepartment) {
                $oldDepartment->removeMember($user);
            }
        }

        // Add to the new department (no-op if already a member)
        if (!$department->isMember($user)) {
            $department->addMember($user);
        }

        // Principal-managed fields
        $user->update([
            'job_scope'   => $validated['job_scope'] ?? null,
            'specialties' => $validated['specialties'] ?? [],
        ]);

        // Notify only if the department actually changed
        if ((int) $oldDepartmentId !== (int) $department->id) {
            NotificationHelper::memberAddedToDepartment($user, $department);
        }

        $this->checkProfileComplete($user);

        // Return to department page if the form was opened from there
        if ($request->get('from') === 'department') {
            return redirect()->route('departments.show', $department)
                ->with('success', "Profile updated for {$user->name}.")
                ->with('notify_refresh', true);
        }

        return redirect()->route('departments.index')
            ->with('success', "Department, job scope and skills updated for {$user->name}.")
            ->with('notify_refresh', true);
    }

    // =========================================================
    // PROFILE COMPLETENESS
    // =========================================================
    private function checkProfileComplete(User $user): void
    {
        $user->refresh();

        if ($user->job_scope && !empty($user->specialties) && $user->department_id) {
            $user->update([
                'is_profile_ready'     => true,
                'profile_completed_at' => $user->profile_completed_at ?? now(),
            ]);
        }
    }

    // =========================================================
    // AUTHORIZE ACCESS
    // =========================================================
    private function authorizeAccess(Department $department): void
    {
        $user = Auth::user();
        $org = $user->currentOrganization();

        if (!$org || $department->organization_id !== $org->id) {
            abort(403, 'You do not have access to this department.');
        }
    }

    // =========================================================
    // AI INSIGHTS
    // =========================================================
    public function getAIInsights(Department $department)
    {
        $this->authorizeAccess($department);

        $insights = [
            'member_skills'         => $this->analyzeMemberSkills($department),
            'workload_distribution' => $this->analyzeWorkload($department),
            'recommendations'       => $this->getRecommendations($department),
        ];

        return response()->json($insights);
    }

    private function analyzeMemberSkills(Department $department): array
    {
        $skills = [];
        foreach ($department->members as $member) {
            $skills[$member->id] = [
                'name'      => $member->name,
                'skills'    => $member->specialties ?? [],
                'job_scope' => $member->job_scope,
            ];
        }
        return $skills;
    }

    private function analyzeWorkload(Department $department): array
    {
        $workload = [];
        foreach ($department->members as $member) {
            $workload[$member->id] = [
                'name'            => $member->name,
                'pending_tasks'   => Task::where('assigned_to', $member->id)
                    ->whereIn('status', ['todo', 'in_progress', 'review'])
                    ->count(),
                'completed_tasks' => Task::where('assigned_to', $member->id)
                    ->where('status', 'done')
                    ->count(),
            ];
        }
        return $workload;
    }

    private function getRecommendations(Department $department): array
    {
        try {
            return $this->aiService->getDepartmentRecommendations($department);
        } catch (\Exception $e) {
            return ['error' => 'AI service unavailable'];
        }
    }
}