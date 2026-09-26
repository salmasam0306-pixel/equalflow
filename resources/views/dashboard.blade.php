@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
/* ===== CARD STYLES ===== */
.dashboard-card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
    border-color: #d1d5db;
}

/* ===== COLORFUL KPI CARDS ===== */
.kpi-card {
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}

.kpi-card .kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    background: rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(4px);
}

.kpi-card .kpi-value {
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.kpi-card .kpi-label {
    font-size: 0.8rem;
    opacity: 0.85;
    font-weight: 500;
}

.kpi-card .kpi-change {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    background: rgba(255, 255, 255, 0.2);
}

.kpi-blue    { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff; }
.kpi-purple  { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: #ffffff; }
.kpi-green   { background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; }
.kpi-orange  { background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); color: #ffffff; }
.kpi-pink    { background: linear-gradient(135deg, #db2777 0%, #be185d 100%); color: #ffffff; }
.kpi-indigo  { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); color: #ffffff; }
.kpi-cyan    { background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: #ffffff; }
.kpi-rose    { background: linear-gradient(135deg, #e11d48 0%, #be123c 100%); color: #ffffff; }
.kpi-amber   { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; }
.kpi-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; }

.kpi-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 80%;
    height: 80%;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    pointer-events: none;
}

.kpi-card::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 60%;
    height: 60%;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 50%;
    pointer-events: none;
}

/* ===== SECTION HEADERS ===== */
.section-header {
    font-weight: 600;
    color: #1f2937;
    letter-spacing: -0.01em;
}

/* ===== TINTED CARD WITH DARK NAVY HEADER ===== */
.tinted-card {
    background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
    border: 1px solid #dbeafe;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    overflow: hidden;
    transition: all 0.3s ease;
}

.tinted-card:hover {
    box-shadow: 0 8px 24px rgba(26, 42, 74, 0.15);
    transform: translateY(-2px);
}

/* Header — dark navy theme */
.tinted-card .card-header {
    padding: 0.9rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
}

.tinted-card .card-header h3 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tinted-card .card-header .header-action {
    font-size: 0.8rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.85);
    transition: color 0.2s ease;
}

.tinted-card .card-header .header-action:hover {
    color: #ffffff;
}

/* Body — tinted blue background */
.tinted-card .card-body {
    padding: 1.25rem 1.5rem;
}

/* ===== DEPARTMENT GROUP HEADER (inside Team Members card) ===== */
.dept-group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.75rem;
    margin-top: 0.75rem;
    margin-bottom: 0.25rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 0.5rem;
    border-left: 3px solid #1a2a4a;
    backdrop-filter: blur(4px);
}

.dept-group-header:first-child {
    margin-top: 0;
}

.dept-group-header .dept-name {
    font-size: 0.7rem;
    font-weight: 700;
    color: #1a2a4a;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.dept-group-header .dept-count {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6b7280;
    background: #ffffff;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    border: 1px solid #e5e7eb;
}

/* ===== BADGE STYLES ===== */
.badge-active      { background: #ecfdf5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.badge-inactive    { background: #f3f4f6; color: #6b7280; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.badge-done        { background: #ecfdf5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.badge-in-progress { background: #eff6ff; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.badge-review      { background: #f5f3ff; color: #5b21b6; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.badge-pending     { background: #fffbeb; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }

/* ===== ACTION BUTTONS — unified dark navy ===== */
.action-btn-primary,
.action-btn-secondary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(26, 42, 74, 0.15);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.action-btn-primary:hover,
.action-btn-secondary:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
    transform: translateY(-1px);
    color: #ffffff;
    text-decoration: none;
}

/* ===== ORGANISATION HEADER ===== */
.company-header {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    border-radius: 1rem;
    padding: 1.5rem;
    color: white;
    box-shadow: 0 4px 20px rgba(26, 42, 74, 0.2);
}

.company-header .invite-code {
    background: rgba(255, 255, 255, 0.15);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: background 0.2s ease;
}
.company-header .invite-code:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 640px) {
    .kpi-card { padding: 1rem; }
    .kpi-card .kpi-icon { width: 40px; height: 40px; font-size: 1rem; }
    .kpi-card .kpi-value { font-size: 1.5rem; }
    .company-header { padding: 1rem; }
    .tinted-card .card-header { padding: 0.75rem 1rem; }
    .tinted-card .card-body { padding: 1rem; }
}
</style>

@php
    $user = auth()->user();
    $org = $data['org'] ?? $user->currentOrganization();

    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

    if ($user->isPrincipal() && $org) {
        $subtitle = 'You have ' . ($data['active_projects'] ?? 0) . ' active ' .
                    \Illuminate\Support\Str::plural('project', $data['active_projects'] ?? 0) .
                    ' and ' . ($data['total_members'] ?? 0) .
                    \Illuminate\Support\Str::plural(' team member', $data['total_members'] ?? 0) . '.';
    } elseif ($user->isLeader() && $org) {
        $subtitle = 'You are leading ' . (isset($data['projects']) ? $data['projects']->count() : 0) .
                    ' ' . \Illuminate\Support\Str::plural('project', isset($data['projects']) ? $data['projects']->count() : 0) . '.';
    } elseif ($user->isMember() && $org) {
        $subtitle = 'You have ' . (isset($data['assigned_tasks']) ? $data['assigned_tasks']->count() : 0) .
                    ' ' . \Illuminate\Support\Str::plural('task', isset($data['assigned_tasks']) ? $data['assigned_tasks']->count() : 0) . ' assigned.';
    } else {
        $subtitle = "Here's what's happening today.";
    }

    $dueSoon = \App\Models\Task::where('assigned_to', $user->id)
        ->whereIn('status', ['todo', 'in_progress'])
        ->whereNotNull('due_date')
        ->whereBetween('due_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
        ->orderBy('due_date')
        ->take(5)
        ->get();
@endphp

{{-- ============================================ --}}
{{-- WELCOME SECTION                             --}}
{{-- ============================================ --}}
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">{{ $greeting }}, {{ $user->name }}!</h2>
    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
</div>

{{-- ============================================ --}}
{{-- QUICK ACTIONS                               --}}
{{-- ============================================ --}}
<div class="mb-8 bg-white rounded-2xl shadow-md border border-gray-200 p-4">
    <div class="flex flex-wrap gap-3">
        @if($user->isPrincipal())
            <a href="{{ route('projects.create') }}" class="action-btn-primary">
                <i class="fas fa-plus"></i> New Project
            </a>
            <a href="{{ route('departments.index') }}" class="action-btn-primary">
                <i class="fas fa-building"></i> Departments
            </a>
        @elseif($user->isLeader())
            <a href="{{ route('projects.index') }}" class="action-btn-primary">
                <i class="fas fa-list-check"></i> My Projects
            </a>
        @elseif($user->isMember())
            <a href="{{ route('projects.index') }}" class="action-btn-primary">
                <i class="fas fa-clipboard-list"></i> View Tasks
            </a>
        @endif

        <a href="{{ route('personal.create') }}" class="action-btn-primary">
            <i class="fas fa-folder-plus"></i> Personal Project
        </a>

        @if(!$org)
            <a href="{{ route('company.create') }}" class="action-btn-primary">
                <i class="fas fa-building"></i> Create Organisation
            </a>
            <a href="{{ route('company.join') }}" class="action-btn-primary">
                <i class="fas fa-link"></i> Join Organisation
            </a>
        @endif
    </div>
</div>

{{-- ============================================ --}}
{{-- ORGANISATION HEADER                        --}}
{{-- ============================================ --}}
@if($org)
    <div class="company-header mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h3 class="text-xl font-bold">{{ $org->name }}</h3>
                <p class="text-indigo-200 text-sm">{{ $org->industry ?? 'Organisation' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="navigator.clipboard.writeText('{{ $org->invite_code }}'); const icon = this.querySelector('.copy-icon'); const label = this.querySelector('.copy-label'); if (icon) icon.className = 'fas fa-check text-emerald-300 copy-icon'; if (label) label.textContent = 'Copied!'; setTimeout(() => { if (icon) icon.className = 'fas fa-copy text-indigo-300 copy-icon'; if (label) label.textContent = 'Invite:'; }, 1500);"
                        class="invite-code flex items-center gap-2 cursor-pointer"
                        title="Click to copy invite code">
                    <i class="fas fa-key text-indigo-300"></i>
                    <span class="text-white/80 copy-label">Invite:</span>
                    <span class="text-white font-mono font-bold">{{ $org->invite_code }}</span>
                    <i class="fas fa-copy text-indigo-300 copy-icon ml-1"></i>
                </button>
            </div>
        </div>
    </div>
@endif

{{-- ============================================ --}}
{{-- PERSONAL USER DASHBOARD                     --}}
{{-- ============================================ --}}
@if($user->role === 'personal')
    @if($org)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6 text-emerald-700 text-sm shadow-sm">
            You are now a member of <strong>{{ $org->name }}</strong>!
        </div>
    @endif

    @php
        $personalProjects = $data['personal_projects'] ?? collect();
        $personalActiveCount = $personalProjects->where('status', 'active')->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="kpi-card kpi-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Personal Projects</p>
                    <p class="kpi-value mt-1">{{ $data['total_projects'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-folder"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Active: {{ $personalActiveCount }}</span></div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Total Tasks</p>
                    <p class="kpi-value mt-1">{{ $data['total_tasks'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">All tasks across projects</span></div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Active Projects</p>
                    <p class="kpi-value mt-1">{{ $personalActiveCount }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">In progress</span></div>
        </div>
    </div>

    @if(!$org)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-8 text-center hover:shadow-lg transition">
                <div class="text-5xl mb-4 text-[#1a2a4a]">
                    <i class="fas fa-building"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Create an Organisation</h3>
                <p class="text-gray-500 text-sm mb-6">Start your own organisation workspace and invite team members.</p>
                <a href="{{ route('company.create') }}" class="action-btn-primary">
                    Create Organisation
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-gray-200 p-8 text-center hover:shadow-lg transition">
                <div class="text-5xl mb-4 text-[#1a2a4a]">
                    <i class="fas fa-link"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Join an Organisation</h3>
                <p class="text-gray-500 text-sm mb-6">Already have an invite code? Join an existing organisation.</p>
                <a href="{{ route('company.join') }}" class="action-btn-primary">
                    Join with Code
                </a>
            </div>
        </div>
    @endif

    <div class="tinted-card">
        <div class="card-header">
            <h3><i class="fas fa-folder"></i> Your Personal Projects</h3>
            <a href="{{ route('personal.index') }}" class="header-action">View All →</a>
        </div>
        <div class="card-body">
            @forelse($personalProjects->take(5) as $project)
                <a href="{{ route('personal.show', $project) }}" class="block p-3 bg-white/70 border border-gray-200 rounded-xl hover:border-[#1a2a4a]/30 hover:bg-white transition mb-2 last:mb-0">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $project->name }}</p>
                            <p class="text-xs text-gray-400">{{ $project->tasks_count ?? 0 }} tasks</p>
                        </div>
                        <span class="badge-{{ $project->status === 'active' ? 'active' : 'inactive' }}">
                            {{ $project->status }}
                        </span>
                    </div>
                </a>
            @empty
                <div class="text-center py-6">
                    <i class="fas fa-folder-open text-3xl text-gray-300 mb-2 block"></i>
                    <p class="text-gray-400 text-sm">No personal projects yet.</p>
                    <a href="{{ route('personal.create') }}" class="inline-block mt-3 text-sm text-[#1a2a4a] hover:underline font-medium">
                        Create your first project →
                    </a>
                </div>
            @endforelse
            @if($personalProjects->count() > 0)
                <a href="{{ route('personal.create') }}" class="block text-center py-2.5 px-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-medium rounded-xl transition text-sm mt-3">
                    Create Personal Project
                </a>
            @endif
        </div>
    </div>
@endif

{{-- ============================================ --}}
{{-- PRINCIPAL DASHBOARD                        --}}
{{-- ============================================ --}}
@if($user->role === 'principal' && $org)
    @php
        $membersByDept = collect($data['members'] ?? [])->groupBy(function ($member) {
            $user = $member->user ?? $member;
            $dept = $user->department ?? null;
            return $dept ? $dept->name : '__unassigned__';
        });

        $membersByDept = $membersByDept->sortKeysUsing(function ($a, $b) {
            if ($a === '__unassigned__') return 1;
            if ($b === '__unassigned__') return -1;
            return strcasecmp($a, $b);
        });
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="kpi-card kpi-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Total Projects</p>
                    <p class="kpi-value mt-1">{{ $data['total_projects'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-folder"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">{{ $data['active_projects'] ?? 0 }} active</span></div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Team Members</p>
                    <p class="kpi-value mt-1">{{ $data['total_members'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Active team</span></div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Active Projects</p>
                    <p class="kpi-value mt-1">{{ $data['active_projects'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">In progress</span></div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Completed</p>
                    <p class="kpi-value mt-1">{{ $data['completed_projects'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-trophy"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Projects done</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- ORGANISATION PROJECTS --}}
        <div class="tinted-card">
            <div class="card-header">
                <h3><i class="fas fa-folder"></i> Organisation Projects</h3>
                <a href="{{ route('projects.index') }}" class="header-action">View All →</a>
            </div>
            <div class="card-body">
                @forelse(($data['projects'] ?? collect())->take(5) as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block p-3 bg-white/70 border border-gray-200 rounded-xl hover:border-[#1a2a4a]/30 hover:bg-white transition mb-2 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $project->name }}</p>
                                <p class="text-xs text-gray-400">Leader: {{ $project->leader?->name ?? 'Not assigned' }}</p>
                            </div>
                            <span class="badge-{{ $project->status === 'active' ? 'active' : 'inactive' }}">
                                {{ $project->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-folder-open text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-400 text-sm">No projects yet.</p>
                        <a href="{{ route('projects.create') }}" class="inline-block mt-3 text-sm text-[#1a2a4a] hover:underline font-medium">
                            Create your first project →
                        </a>
                    </div>
                @endforelse
                @if(($data['projects'] ?? collect())->count() > 0)
                    <a href="{{ route('projects.create') }}" class="block text-center py-2.5 px-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-medium rounded-xl transition text-sm mt-3">
                        Create New Project
                    </a>
                @endif
            </div>
        </div>

        {{-- TEAM MEMBERS --}}
        <div class="tinted-card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> Team Members</h3>
                <a href="{{ route('departments.index') }}" class="header-action">Manage →</a>
            </div>
            <div class="card-body">
                @if($membersByDept->count() > 0)
                    <div class="max-h-96 overflow-y-auto pr-1">
                        @foreach($membersByDept as $deptName => $deptMembers)
                            @php
                                $isUnassigned = ($deptName === '__unassigned__');
                                $displayName = $isUnassigned ? 'No Department' : $deptName;
                            @endphp
                            <div class="dept-group-header">
                                <span class="dept-name">
                                    @if($isUnassigned)
                                        <i class="fas fa-user-slash mr-1 opacity-70"></i>
                                    @else
                                        <i class="fas fa-building mr-1 opacity-70"></i>
                                    @endif
                                    {{ $displayName }}
                                </span>
                                <span class="dept-count">{{ $deptMembers->count() }}</span>
                            </div>

                            @foreach($deptMembers as $member)
                                @php
                                    $memberUser = $member->user ?? $member;
                                    $memberRole = $member->role ?? ($memberUser->role ?? 'member');
                                @endphp
                                <div class="flex items-center justify-between py-2 px-2 border-b border-gray-100 last:border-0">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $memberUser->name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $memberUser->email }}</p>
                                    </div>
                                    <span class="text-xs px-2.5 py-1 rounded-full font-medium ml-2 flex-shrink-0
                                        @if($memberRole === 'principal') bg-purple-100 text-purple-700
                                        @elseif($memberRole === 'leader') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-600
                                        @endif">
                                        @if($memberRole === 'principal') Principal
                                        @elseif($memberRole === 'leader') Leader
                                        @else Member
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fas fa-users text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-400 text-sm">No members yet.</p>
                        <a href="{{ route('company.join') }}" class="inline-block mt-3 text-sm text-[#1a2a4a] hover:underline font-medium">
                            Invite members →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- ============================================ --}}
{{-- LEADER DASHBOARD                           --}}
{{-- ============================================ --}}
@if($user->role === 'leader' && $org)
    @php
        $leaderProjects = $data['projects'] ?? collect();
        $leaderTasks = $data['tasks'] ?? collect();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="kpi-card kpi-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">My Projects</p>
                    <p class="kpi-value mt-1">{{ $leaderProjects->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-folder"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Leading {{ $leaderProjects->count() }} projects</span></div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Total Tasks</p>
                    <p class="kpi-value mt-1">{{ $data['total_tasks'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Across all projects</span></div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Completed</p>
                    <p class="kpi-value mt-1">{{ $data['completed_tasks'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Tasks done</span></div>
        </div>

        <div class="kpi-card kpi-rose">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Pending</p>
                    <p class="kpi-value mt-1">{{ $data['pending_tasks'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Awaiting completion</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="tinted-card">
            <div class="card-header">
                <h3><i class="fas fa-folder"></i> My Projects</h3>
            </div>
            <div class="card-body">
                @forelse($leaderProjects as $project)
                    <a href="{{ route('projects.show', $project) }}" class="block p-3 bg-white/70 border border-gray-200 rounded-xl hover:border-[#1a2a4a]/30 hover:bg-white transition mb-2 last:mb-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $project->name }}</p>
                                <p class="text-xs text-gray-400">{{ $project->tasks?->count() ?? 0 }} tasks</p>
                            </div>
                            <span class="badge-{{ $project->status === 'active' ? 'active' : 'inactive' }}">
                                {{ $project->status }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-folder-open text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-400 text-sm">No projects yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="tinted-card">
            <div class="card-header">
                <h3><i class="fas fa-clipboard-list"></i> Recent Tasks</h3>
                <a href="{{ route('projects.index') }}" class="header-action">View All →</a>
            </div>
            <div class="card-body">
                @forelse($leaderTasks->take(5) as $task)
                    <div class="flex items-center justify-between py-2 px-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $task->title }}</p>
                            <p class="text-xs text-gray-400">{{ $task->project?->name ?? '—' }} • {{ $task->assignee?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="badge-{{ $task->status === 'done' ? 'done' : ($task->status === 'in_progress' ? 'in-progress' : ($task->status === 'review' ? 'review' : 'pending')) }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-clipboard-list text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-400 text-sm">No tasks yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif

{{-- ============================================ --}}
{{-- MEMBER DASHBOARD                           --}}
{{-- ============================================ --}}
@if($user->role === 'member' && $org)
    @php
        $assignedTasks = $data['assigned_tasks'] ?? collect();
        $completedTasks = $data['completed_tasks'] ?? collect();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="kpi-card kpi-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Assigned Tasks</p>
                    <p class="kpi-value mt-1">{{ $assignedTasks->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clipboard-list"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Tasks assigned to you</span></div>
        </div>

        <div class="kpi-card kpi-green">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Completed</p>
                    <p class="kpi-value mt-1">{{ $completedTasks->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Tasks completed</span></div>
        </div>

        <div class="kpi-card kpi-purple">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Total Tasks</p>
                    <p class="kpi-value mt-1">{{ $data['total_tasks'] ?? 0 }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-chart-simple"></i></div>
            </div>
            <div class="mt-3"><span class="kpi-change">Overall workload</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="tinted-card">
            <div class="card-header">
                <h3><i class="fas fa-clipboard-list"></i> My Assigned Tasks</h3>
            </div>
            <div class="card-body">
                @forelse($assignedTasks as $task)
                    <div class="flex items-center justify-between py-2 px-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $task->title }}</p>
                            <p class="text-xs text-gray-400">{{ $task->project?->name ?? '—' }}</p>
                        </div>
                        <span class="badge-{{ $task->status === 'done' ? 'done' : ($task->status === 'in_progress' ? 'in-progress' : ($task->status === 'review' ? 'review' : 'pending')) }}">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-clipboard-check text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-400 text-sm">No tasks assigned.</p>
                        <a href="{{ route('projects.index') }}" class="inline-block mt-3 text-sm text-[#1a2a4a] hover:underline font-medium">
                            View projects →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="tinted-card">
            <div class="card-header">
                <h3><i class="fas fa-check-circle"></i> Completed Tasks</h3>
            </div>
            <div class="card-body">
                @forelse($completedTasks as $task)
                    <div class="flex items-center justify-between py-2 px-2 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $task->title }}</p>
                            <p class="text-xs text-gray-400">{{ $task->project?->name ?? '—' }}</p>
                        </div>
                        <span class="badge-done">Done</span>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-trophy text-3xl text-gray-300 mb-2 block"></i>
                        <p class="text-gray-400 text-sm">No completed tasks yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endif

{{-- ============================================ --}}
{{-- DUE THIS WEEK                               --}}
{{-- ============================================ --}}
@if($dueSoon->count() > 0)
    <div class="mt-8 tinted-card">
        <div class="card-header">
            <h3><i class="fas fa-clock"></i> Due This Week</h3>
            <span class="text-xs text-white/80">{{ $dueSoon->count() }} task{{ $dueSoon->count() !== 1 ? 's' : '' }}</span>
        </div>
        <div class="card-body">
            @foreach($dueSoon as $task)
                <a href="{{ $task->isSub() ? route('tasks.subtask.show', $task) : route('tasks.show', $task) }}"
                   class="block p-3 bg-white/70 border border-gray-200 rounded-xl hover:border-[#1a2a4a]/30 hover:bg-white transition mb-2 last:mb-0">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $task->title }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                <i class="fas fa-calendar text-gray-300 mr-1"></i>
                                {{ $task->due_date->format('d M Y') }}
                                <span class="text-rose-500 font-medium ml-1">• {{ $task->due_date->diffForHumans() }}</span>
                            </p>
                        </div>
                        <span class="badge-{{ $task->status === 'done' ? 'done' : ($task->status === 'in_progress' ? 'in-progress' : ($task->status === 'review' ? 'review' : 'pending')) }} ml-3">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

@endsection