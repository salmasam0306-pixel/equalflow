<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\OrganizationMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserStatusController extends Controller
{
    /**
     * Show the status update form
     */
    public function edit()
    {
        $user = auth()->user();
        
        // Check if status should be auto-updated
        $user->checkAndUpdateStatus();
        
        return view('profile.status', compact('user'));
    }

    /**
     * Update user status with validation
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        // Base validation
        $rules = [
            'status' => 'required|in:active,outstation,annual_leave,medical_leave',
            'status_note' => 'nullable|string|max:500',
        ];

        // If status is not active, require date
        if ($request->status !== 'active') {
            $rules['status_until'] = 'required|date|after:today';
        } else {
            $rules['status_until'] = 'nullable|date';
        }

        $request->validate($rules, [
            'status_until.required' => 'Please select a return date.',
            'status_until.after' => 'The return date must be in the future.',
        ]);

        // If status is active, clear the until date
        $statusUntil = $request->status === 'active' ? null : $request->status_until;

        $user->update([
            'status' => $request->status,
            'status_until' => $statusUntil,
            'status_note' => $request->status_note,
            'status_updated_at' => now(),
        ]);

        $statusLabel = $user->getStatusLabel();

        return redirect()->route('profile.edit')
            ->with('success', "Status updated to: {$statusLabel}");
    }

    /**
     * Quick status update via AJAX
     */
    public function quickUpdate(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'status' => 'required|in:active,outstation,annual_leave,medical_leave',
            'status_until' => 'required_if:status,outstation,annual_leave,medical_leave|date|after:today',
        ]);

        $statusUntil = $request->status === 'active' ? null : $request->status_until;

        $user->update([
            'status' => $request->status,
            'status_until' => $statusUntil,
            'status_updated_at' => now(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $user->status,
                'label' => $user->getStatusLabel(),
                'color' => $user->getStatusColor(),
                'icon' => $user->getStatusIcon(),
                'until' => $user->status_until?->format('Y-m-d'),
            ]);
        }

        return redirect()->back()->with('success', 'Status updated!');
    }

    /**
     * Get status for a specific user (AJAX)
     */
    public function getStatus($userId)
    {
        $user = User::findOrFail($userId);
        
        // Auto-update status if needed
        $user->checkAndUpdateStatus();
        
        return response()->json([
            'status' => $user->status,
            'label' => $user->getStatusLabel(),
            'color' => $user->getStatusColor(),
            'icon' => $user->getStatusIcon(),
            'until' => $user->status_until?->format('d M Y'),
            'note' => $user->status_note,
        ]);
    }

    /**
     * Get all users' status for a company (AJAX)
     */
    public function getTeamStatus()
    {
        $user = auth()->user();
        $org = $user->currentOrganization();
        
        if (!$org) {
            return response()->json([]);
        }

        $members = OrganizationMember::where('organization_id', $org->id)
            ->with('user')
            ->get()
            ->pluck('user');

        $statuses = [];
        foreach ($members as $member) {
            // Auto-update status if needed
            $member->checkAndUpdateStatus();
            
            $statuses[] = [
                'id' => $member->id,
                'name' => $member->name,
                'status' => $member->status,
                'label' => $member->getStatusLabel(),
                'color' => $member->getStatusColor(),
                'icon' => $member->getStatusIcon(),
                'until' => $member->status_until?->format('d M Y'),
                'note' => $member->status_note,
            ];
        }

        return response()->json($statuses);
    }
}