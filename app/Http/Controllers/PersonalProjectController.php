<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\ProjectMember;
use App\Helpers\NotificationHelper; // ✅ ADD THIS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonalProjectController extends Controller
{
    /**
     * Display a listing of personal projects.
     */
    public function index()
    {
        $projects = Project::where('created_by', auth()->id())
            ->where('type', 'personal')
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        // Also get projects where user is invited
        $invitedProjects = Project::where('type', 'personal')
            ->whereHas('members', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->where('created_by', '!=', auth()->id())
            ->withCount('tasks')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('personal.index', compact('projects', 'invitedProjects'));
    }

    /**
     * Show the form for creating a new personal project.
     */
    public function create()
    {
        return view('personal.create');
    }

    /**
     * Store a newly created personal project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date'
        ]);

        // Generate invite code
        $inviteCode = 'P' . strtoupper(Str::random(5));

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'leader_id' => auth()->id(),
            'type' => 'personal',
            'status' => 'active',
            'deadline' => $request->deadline,
            'invite_code' => $inviteCode
        ]);

        // Add creator as member
        $exists = ProjectMember::where('project_id', $project->id)
            ->where('user_id', auth()->id())
            ->exists();

        if (!$exists) {
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
            ]);
        }

        // ✅ SEND NOTIFICATION: Personal Project Created
        NotificationHelper::systemNotification(
            auth()->id(),
            "Personal Project Created: {$project->name} 📁",
            "Your personal project '{$project->name}' has been created successfully! Invite code: {$inviteCode}",
            route('personal.show', $project)
        );

        return redirect()->route('personal.show', $project)
            ->with('success', 'Personal project created successfully! Invite Code: ' . $inviteCode);
    }

    /**
     * Display the specified personal project.
     */
    public function show(Project $project)
    {
        // Check if user is the creator OR has been invited
        $isCreator = $project->created_by === auth()->id();
        $isInvited = $project->members()->where('user_id', auth()->id())->exists();
        
        if (!$isCreator && !$isInvited) {
            abort(403, 'You do not have access to this personal project.');
        }

        // Load tasks with assignee and creator
        $project->load(['tasks' => function($q) {
            $q->with(['assignee', 'creator']);
        }, 'creator']);

        // Get members
        $members = $project->members()
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        return view('personal.show', compact('project', 'members'));
    }

    /**
     * Show the invite page.
     */
    public function invite(Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            abort(403, 'Only the project creator can invite people.');
        }

        // Get users who are NOT already members
        $existingMemberIds = $project->members()->pluck('user_id')->toArray();
        
        $users = User::where('id', '!=', auth()->id())
            ->whereNotIn('id', $existingMemberIds)
            ->select('id', 'name', 'email')
            ->get();

        return view('personal.invite', compact('project', 'users'));
    }

    /**
     * Send invite to a user.
     */
    public function sendInvite(Request $request, Project $project)
    {
        if ($project->created_by !== auth()->id()) {
            abort(403, 'Only the project creator can invite people.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::find($request->user_id);

        // Check if user is already a member
        if ($project->members()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'This user is already a member of the project.');
        }

        // Add user as a member
        $project->members()->attach($user->id);

        // ✅ SEND NOTIFICATION: User Invited to Personal Project
        NotificationHelper::systemNotification(
            $user->id,
            "You're Invited: {$project->name} 📩",
            "You have been invited to join the personal project '{$project->name}' by {$project->creator->name}.",
            route('personal.show', $project)
        );

        return redirect()->route('personal.invite', $project)
            ->with('success', 'User invited successfully to the project!');
    }

    /**
     * Remove a member from the project.
     */
    public function removeMember(Request $request, Project $project)
    {
        // Only the project creator can remove members
        if ($project->created_by !== auth()->id()) {
            abort(403, 'Only the project creator can remove members.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $userId = $request->user_id;

        // Cannot remove yourself
        if ($userId == auth()->id()) {
            return redirect()->back()->with('error', 'You cannot remove yourself as the creator.');
        }

        // Check if user is a member
        if (!$project->members()->where('user_id', $userId)->exists()) {
            return redirect()->back()->with('error', 'User is not a member of this project.');
        }

        // Check if user has any assigned tasks in this project
        $hasTasks = Task::where('project_id', $project->id)
            ->where('assigned_to', $userId)
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->exists();

        if ($hasTasks) {
            return redirect()->back()->with('error', 'Cannot remove member. They have active tasks assigned. Please reassign their tasks first.');
        }

        // Get user name for notification
        $removedUser = User::find($userId);
        $userName = $removedUser ? $removedUser->name : 'User';

        // Remove the member
        $project->members()->detach($userId);

        // Unassign any tasks they had
        Task::where('project_id', $project->id)
            ->where('assigned_to', $userId)
            ->update(['assigned_to' => null]);

        // ✅ SEND NOTIFICATION: Member Removed
        NotificationHelper::systemNotification(
            $userId,
            "Removed from: {$project->name} 🚫",
            "You have been removed from the personal project '{$project->name}'.",
            route('personal.index')
        );

        return redirect()->route('personal.invite', $project)
            ->with('success', 'Member removed successfully!');
    }

    /**
     * Leave a personal project (for invited members).
     */
    public function leave(Project $project)
    {
        if ($project->created_by === auth()->id()) {
            return redirect()->back()->with('error', 'You are the creator. You cannot leave your own project.');
        }

        if (!$project->members()->where('user_id', auth()->id())->exists()) {
            return redirect()->back()->with('error', 'You are not a member of this project.');
        }

        // Check if user has any active tasks
        $hasActiveTasks = Task::where('project_id', $project->id)
            ->where('assigned_to', auth()->id())
            ->whereIn('status', ['todo', 'in_progress', 'review'])
            ->exists();

        if ($hasActiveTasks) {
            return redirect()->back()->with('error', 'You have active tasks assigned. Please complete them or ask the creator to reassign them before leaving.');
        }

        // ✅ SEND NOTIFICATION: User Left Project
        NotificationHelper::systemNotification(
            $project->created_by,
            "Member Left: {$project->name} 👋",
            auth()->user()->name . " has left your personal project '{$project->name}'.",
            route('personal.show', $project)
        );

        $project->members()->detach(auth()->id());

        return redirect()->route('personal.index')
            ->with('success', 'You have left the project successfully.');
    }

    /**
     * Show the join by code page.
     */
    public function showJoinPage()
    {
        return view('personal.join');
    }

    /**
     * Join a personal project via invite code.
     */
    public function joinByCode(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:projects,invite_code'
        ]);

        $project = Project::where('invite_code', strtoupper($request->invite_code))
            ->where('type', 'personal')
            ->first();

        if (!$project) {
            return back()->with('error', 'Invalid invite code. Please try again.');
        }

        if ($project->created_by === auth()->id()) {
            return redirect()->route('personal.show', $project)
                ->with('info', 'This is your project!');
        }

        if ($project->members()->where('user_id', auth()->id())->exists()) {
            return redirect()->route('personal.show', $project)
                ->with('info', 'You are already a member of this project.');
        }

        // Add user as a member
        $project->members()->attach(auth()->id());

        // ✅ SEND NOTIFICATION: User Joined Personal Project
        NotificationHelper::systemNotification(
            auth()->id(),
            "You joined: {$project->name} 🎉",
            "You have successfully joined the personal project '{$project->name}'.",
            route('personal.show', $project)
        );

        // ✅ SEND NOTIFICATION: Notify creator that someone joined
        if ($project->created_by !== auth()->id()) {
            NotificationHelper::systemNotification(
                $project->created_by,
                "New Member Joined: {$project->name} 👤",
                auth()->user()->name . " has joined your personal project '{$project->name}'.",
                route('personal.show', $project)
            );
        }

        return redirect()->route('personal.show', $project)
            ->with('success', 'Welcome to the project!');
    }

    /**
     * Delete a personal project.
     */
    public function destroy(Project $project)
    {
        // Check if user is the creator
        if ($project->created_by !== auth()->id()) {
            abort(403, 'You do not have permission to delete this personal project.');
        }

        // Check if it's a personal project
        if ($project->type !== 'personal') {
            abort(403, 'This is not a personal project.');
        }

        // ✅ SEND NOTIFICATION: Personal Project Deleted
        foreach ($project->members as $member) {
            NotificationHelper::systemNotification(
                $member->id,
                "Project Deleted: {$project->name} 🗑️",
                "The personal project '{$project->name}' has been deleted.",
                route('personal.index')
            );
        }

        // Detach all members
        $project->members()->detach();
        
        // Delete the project
        $project->delete();

        return redirect()->route('personal.index')
            ->with('success', 'Personal project deleted successfully!');
    }
}