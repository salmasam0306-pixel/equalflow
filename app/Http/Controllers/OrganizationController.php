<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Helpers\NotificationHelper;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function create()
    {
        if (auth()->user()->organizations()->exists()) {
            return redirect()->route('dashboard')
                ->with('info', 'You are already in a company.');
        }

        return view('company.create');
    }

    public function settings()
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can access company settings.');
        }

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'You need to create a company first.');
        }

        $totalMembers      = OrganizationMember::where('organization_id', $org->id)->count();
        $totalProjects     = \App\Models\Project::where('organization_id', $org->id)->count();
        $activeProjects    = \App\Models\Project::where('organization_id', $org->id)
            ->where('status', 'active')->count();
        $completedProjects = \App\Models\Project::where('organization_id', $org->id)
            ->where('status', 'completed')->count();

        return view('company.settings', compact(
            'org', 'totalMembers', 'totalProjects', 'activeProjects', 'completedProjects'
        ));
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can update company settings.');
        }

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'You need to create a company first.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'industry'    => 'nullable|string|max:255',
            'logo'        => 'nullable|image|max:2048|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $oldName = $org->name;

        if ($request->hasFile('logo')) {
            if ($org->logo) {
                Storage::disk('public')->delete('company_logos/' . $org->logo);
            }

            $file     = $request->file('logo');
            $filename = time() . '_' . $org->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('company_logos', $filename, 'public');
            $org->logo = $filename;
        }

        $org->name        = $request->name;
        $org->description = $request->description;
        $org->industry    = $request->industry;
        $org->save();

        if ($oldName !== $request->name) {
            foreach ($org->members as $member) {
                NotificationHelper::systemNotification(
                    $member->id,
                    "Company Name Changed",
                    "The company name has been changed from '{$oldName}' to '{$request->name}'.",
                    route('dashboard')
                );
            }
        }

        return redirect()->route('company.settings')
            ->with('success', 'Company settings updated successfully!');
    }

    public function removeLogo()
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can remove the company logo.');
        }

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('dashboard')
                ->with('error', 'No company found.');
        }

        if ($org->logo) {
            Storage::disk('public')->delete('company_logos/' . $org->logo);
            $org->logo = null;
            $org->save();

            return redirect()->route('company.settings')
                ->with('success', 'Company logo removed successfully!');
        }

        return redirect()->route('company.settings')
            ->with('info', 'No logo to remove.');
    }

    public function regenerateInviteCode()
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can regenerate the invite code.');
        }

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('company.create')
                ->with('error', 'You need to create a company first.');
        }

        $newCode = strtoupper(Str::random(6));
        $oldCode = $org->invite_code;

        $org->update(['invite_code' => $newCode]);

        foreach ($org->members as $member) {
            NotificationHelper::systemNotification(
                $member->id,
                "Company Invite Code Updated",
                "The company invite code has been updated. New code: {$newCode}",
                route('company.settings')
            );
        }

        return redirect()->route('company.settings')
            ->with('success', 'Invite code regenerated successfully! New code: ' . $newCode);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'industry'    => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        if ($user->organizations()->exists()) {
            return redirect()->route('dashboard')
                ->with('error', 'You are already a member of a company.');
        }

        $inviteCode = strtoupper(Str::random(6));

        $org = Organization::create([
            'name'        => $request->name,
            'description' => $request->description,
            'industry'    => $request->industry,
            'invite_code' => $inviteCode,
            'created_by'  => $user->id,
            'logo'        => null,
        ]);

        OrganizationMember::create([
            'organization_id' => $org->id,
            'user_id'         => $user->id,
            'role'            => 'principal',
        ]);

        // ✅ FIXED: was `profile_completed` (dropped column)
        $user->update([
            'role'                 => 'principal',
            'is_profile_ready'     => true,
            'profile_completed_at' => $user->profile_completed_at ?? now(),
        ]);

        NotificationHelper::systemNotification(
            $user->id,
            "Company Created: {$org->name} 🏢",
            "Your company '{$org->name}' has been created successfully! Invite code: {$inviteCode}",
            route('company.settings')
        );

        return redirect()->route('dashboard')
            ->with('success', 'Company created! Invite code: ' . $inviteCode);
    }

    public function showJoin()
    {
        if (auth()->user()->organizations()->exists()) {
            return redirect()->route('dashboard')
                ->with('info', 'You are already in a company.');
        }

        return view('company.join');
    }

    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:organizations,invite_code',
        ]);

        $org = Organization::where('invite_code', strtoupper($request->invite_code))->first();

        if (!$org) {
            return back()->with('error', 'Invalid invite code. Please try again.');
        }

        $exists = OrganizationMember::where('organization_id', $org->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'You are already a member of this company.');
        }

        if (auth()->user()->organizations()->exists()) {
            return back()->with('error', 'You are already in a company. Leave your current company first.');
        }

        OrganizationMember::create([
            'organization_id' => $org->id,
            'user_id'         => auth()->id(),
            'role'            => 'member',
        ]);

        // ✅ FIXED: was `profile_completed` (dropped column)
        auth()->user()->update([
            'role'                 => 'member',
            'is_profile_ready'     => false,
            'profile_completed_at' => null,
        ]);

        $user = auth()->user();

        // Notify the joiner
        NotificationHelper::systemNotification(
            $user->id,
            "You joined: {$org->name} 👋",
            "You have successfully joined the company '{$org->name}'.",
            route('dashboard')
        );

        // ============================================================
        // NOTIFY THE PRINCIPAL — enhanced "member_joined" alert
        // ============================================================
        $principal = OrganizationMember::where('organization_id', $org->id)
            ->where('role', 'principal')
            ->first();

        if ($principal && $principal->user_id !== $user->id) {
            app(NotificationService::class)->notify(
                $principal->user_id,
                'member_joined',                     // dedicated, filterable type
                "New Member Joined: {$user->name}",
                "{$user->name} ({$user->email}) has joined '{$org->name}' as a new member.",
                [
                    'link'            => route('departments.index'),
                    'sender_id'       => $user->id,
                    'icon'            => 'fa-user-plus',
                    'color'           => 'green',
                    'action_text'     => 'Assign to Department',
                    'member_id'       => $user->id,
                    'organization_id' => $org->id,
                ]
            );
        }

        return redirect()->route('profile.setup')
            ->with('success', 'Successfully joined "' . $org->name . '"! Please complete your profile.');
    }

    public function destroy()
    {
        $user = auth()->user();

        if (!$user->isPrincipal()) {
            abort(403, 'Only the Principal can delete the company.');
        }

        $org = $user->currentOrganization();
        if (!$org) {
            return redirect()->route('dashboard')
                ->with('error', 'No company found.');
        }

        $orgName = $org->name;

        foreach ($org->members as $member) {
            NotificationHelper::systemNotification(
                $member->id,
                "Company Deleted: {$orgName}",
                "The company '{$orgName}' has been deleted.",
                route('dashboard')
            );
        }

        if ($org->logo) {
            Storage::disk('public')->delete('company_logos/' . $org->logo);
        }

        OrganizationMember::where('organization_id', $org->id)->delete();
        \App\Models\Project::where('organization_id', $org->id)->delete();
        $org->delete();

        $user->update(['role' => 'personal']);

        return redirect()->route('dashboard')
            ->with('success', 'Company deleted successfully.');
    }
}