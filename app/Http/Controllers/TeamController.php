<?php

namespace App\Http\Controllers;

use App\Models\OrganizationMember;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display the team members page
     */
    public function index()
    {
        $user = auth()->user();
        $org = $user->currentOrganization();
        $user->checkAndUpdateStatus();
    
        // Get all members and update their status
        $members = OrganizationMember::where('organization_id', $org->id)
            ->with('user')
            ->get();
        
        foreach ($members as $member) {
            $member->user->checkAndUpdateStatus();
    }
        
        // If user is not in a company, redirect
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'You need to be in a company to view team members.');
        }

        // Get all members of the organization
        $members = OrganizationMember::where('organization_id', $org->id)
            ->with('user')
            ->get();

        // Get the current user's role in the organization
        $currentMember = OrganizationMember::where('organization_id', $org->id)
            ->where('user_id', $user->id)
            ->first();

        return view('team.index', compact('members', 'org', 'currentMember'));
    }

    /**
     * Get team member details (for AJAX)
     */
    public function show($id)
    {
        $member = OrganizationMember::where('organization_id', auth()->user()->currentOrganization()->id)
            ->where('user_id', $id)
            ->with('user')
            ->firstOrFail();

        return response()->json($member);
    }

    /**
     * Remove a member from the company (Principal only)
     */
    public function removeMember($userId)
    {
        $user = auth()->user();
        $org = $user->currentOrganization();

        if (!$org) {
            return redirect()->back()->with('error', 'You are not in a company.');
        }

        // Only Principal can remove members
        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can remove members from the company.');
        }

        // Cannot remove yourself
        if ($userId == $user->id) {
            return redirect()->back()->with('error', 'You cannot remove yourself as Principal.');
        }

        // Cannot remove other Principals (if multiple principals exist)
        $memberToRemove = OrganizationMember::where('organization_id', $org->id)
            ->where('user_id', $userId)
            ->first();

        if (!$memberToRemove) {
            return redirect()->back()->with('error', 'Member not found in this company.');
        }

        if ($memberToRemove->role === 'principal') {
            return redirect()->back()->with('error', 'Cannot remove another Principal.');
        }

        // Remove the member
        $memberToRemove->delete();

        // If the user was a leader, update their role back to member
        $userToRemove = User::find($userId);
        if ($userToRemove && $userToRemove->role === 'leader') {
            $userToRemove->update(['role' => 'member']);
        }

        return redirect()->back()->with('success', 'Member removed from the company successfully.');
    }

    /**
     * Leave the company (self-removal)
     */
    public function leaveCompany()
    {
        $user = auth()->user();
        $org = $user->currentOrganization();

        if (!$org) {
            return redirect()->back()->with('error', 'You are not in a company.');
        }

        // Principal cannot leave
        if ($user->isPrincipal()) {
            return redirect()->back()->with('error', 'Principals cannot leave the company. You must delete the company or assign a new Principal.');
        }

        // Remove user from organization
        OrganizationMember::where('organization_id', $org->id)
            ->where('user_id', $user->id)
            ->delete();

        // Update user role back to personal
        $user->update([
            'role' => 'personal',
            'profile_completed' => false
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'You have left the company successfully.');
    }
}