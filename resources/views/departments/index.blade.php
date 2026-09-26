{{-- resources/views/departments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Departments')

@section('content')

<style>
/* ============================================ */
/* LAYOUT                                       */
/* ============================================ */
.departments-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.75rem;
}

@media (max-width: 1024px) {
    .departments-layout {
        grid-template-columns: 1fr;
    }
}

/* ============================================ */
/* KPI CARDS (colorful)                         */
/* ============================================ */
.kpi-card {
    border-radius: 1rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    color: #ffffff;
}
.kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
}
.kpi-card .kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
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
    opacity: 0.9;
    font-weight: 500;
}
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

.kpi-blue    { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
.kpi-indigo  { background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%); }
.kpi-amber   { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }
.kpi-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

/* ============================================ */
/* DEPARTMENT CARD                              */
/* ============================================ */
.dept-card {
    background: #ffffff;
    border-radius: 1.25rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    overflow: hidden;
    cursor: pointer;
}

.dept-card:hover {
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
    transform: translateY(-3px);
}

.dept-card-link {
    display: block;
    text-decoration: none;
    color: inherit;
}
.dept-card-link:hover { text-decoration: none; color: inherit; }
.dept-card .no-card-link { position: relative; z-index: 2; }

/* Header now dark navy */
.dept-header {
    padding: 1.15rem 1.5rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    position: relative;
}
.dept-header::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -10%;
    width: 55%;
    height: 90%;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 50%;
    pointer-events: none;
}
.dept-header h4 {
    font-size: 1.05rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    position: relative;
    z-index: 1;
}
.dept-header .dept-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
}
.dept-status-badge.active {
    background: rgba(34, 197, 94, 0.25);
    color: #d1fae5;
    border: 1px solid rgba(34, 197, 94, 0.4);
}
.dept-status-badge.inactive {
    background: rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.7);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.dept-body {
    padding: 1.35rem 1.5rem;
}

.member-item {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.55rem 0.7rem;
    border-radius: 0.65rem;
    transition: all 0.2s ease;
}
.member-item:hover { background: #f8fafc; }

/* ============================================ */
/* SIDEBAR MEMBER LIST                          */
/* ============================================ */
.member-list-card {
    background: #ffffff;
    border-radius: 1.25rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    position: sticky;
    top: 80px;
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.member-list-card::-webkit-scrollbar { width: 6px; }
.member-list-card::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.member-list-card::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
.member-list-card::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

/* Sidebar header — dark navy to match department cards */
.member-list-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    border-radius: 1.25rem 1.25rem 0 0;
    position: relative;
    overflow: hidden;
}
.member-list-header::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -10%;
    width: 55%;
    height: 90%;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 50%;
    pointer-events: none;
}
.member-list-header > * {
    position: relative;
    z-index: 1;
}

.member-list-item {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 0.85rem 1.15rem;
    border-bottom: 1px solid #f3f4f6;
    transition: all 0.2s ease;
    cursor: pointer;
}
.member-list-item:hover { background: #f9fafb; }
.member-list-item:last-child { border-bottom: none; }

/* ============================================ */
/* BUTTONS                                      */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.7rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    box-shadow: 0 4px 15px rgba(26, 42, 74, 0.3);
    transform: translateY(-1px);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    padding: 0.7rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.95rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.btn-secondary:hover { background: #e5e7eb; transform: translateY(-1px); }

/* ============================================ */
/* SEARCH INPUT                                 */
/* ============================================ */
.search-input {
    transition: all 0.3s ease;
    border-color: #e5e7eb;
}
.search-input:focus {
    border-color: #1a2a4a;
    box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1);
    outline: none;
}
.search-input::placeholder { color: #9ca3af; }

/* ============================================ */
/* MEMBER DETAIL POPUP                          */
/* ============================================ */
.member-popup {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
    opacity: 0;
    transform: translateY(6px) scale(0.98);
    transition: opacity 0.18s ease, transform 0.18s ease;
    width: 380px;
}
.member-popup.visible {
    opacity: 1;
    transform: translateY(0) scale(1);
}

.member-popup-inner {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16), 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.member-popup-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.15rem 1.25rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    position: relative;
}

.member-popup-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.3);
    flex-shrink: 0;
}

.member-popup-name {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    line-height: 1.3;
}

.member-popup-email {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.75);
    margin: 0.2rem 0 0 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.member-popup-status {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    backdrop-filter: blur(4px);
}

.member-popup-body {
    padding: 1rem 1.25rem;
}

.member-popup-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.6rem 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.9rem;
}
.member-popup-row:last-child { border-bottom: none; }

.member-popup-label {
    color: #6b7280;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}
.member-popup-label i { color: #9ca3af; font-size: 0.85rem; }

.member-popup-value {
    color: #111827;
    font-weight: 500;
    text-align: right;
    word-break: break-word;
}

.member-popup-value a {
    color: #1a2a4a;
    text-decoration: none;
    border-bottom: 1px dashed transparent;
    transition: border-color 0.15s ease;
}
.member-popup-value a:hover {
    border-bottom-color: #1a2a4a;
}

.member-popup-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
    justify-content: flex-end;
}

.member-popup-tag {
    display: inline-block;
    padding: 0.25rem 0.7rem;
    border-radius: 9999px;
    font-size: 0.8rem;
    font-weight: 500;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #dbeafe;
}

.member-popup-status.status-active { background: rgba(34, 197, 94, 0.35); border-color: rgba(34, 197, 94, 0.5); }
.member-popup-status.status-outstation { background: rgba(59, 130, 246, 0.35); border-color: rgba(59, 130, 246, 0.5); }
.member-popup-status.status-annual_leave { background: rgba(234, 179, 8, 0.35); border-color: rgba(234, 179, 8, 0.5); }
.member-popup-status.status-medical_leave { background: rgba(239, 68, 68, 0.35); border-color: rgba(239, 68, 68, 0.5); }

.member-popup-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    margin: 0.75rem 0;
}

/* ============================================ */
/* LEADS TAGS                                   */
/* ============================================ */
#popupLeads .member-popup-lead-tag {
    display: inline-block;
    padding: 0.25rem 0.7rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}
#popupLeads .member-popup-lead-tag:hover {
    background: #fde68a;
    color: #78350f;
    text-decoration: none;
}

/* ============================================ */
/* WORKLOAD                                     */
/* ============================================ */
.member-popup-workload { padding-top: 0.25rem; }

.member-popup-workload-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.member-popup-workload-title i { color: #9ca3af; }

.member-popup-workload-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.65rem;
}

.member-popup-workload-item {
    text-align: center;
    padding: 0.65rem 0.35rem;
    background: #f8fafc;
    border-radius: 0.6rem;
    border: 1px solid #f1f5f9;
}

.member-popup-workload-item .w-total,
.member-popup-workload-item .w-pending,
.member-popup-workload-item .w-completed {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1;
}
.member-popup-workload-item .w-total { color: #1a2a4a; }
.member-popup-workload-item .w-pending { color: #d97706; }
.member-popup-workload-item .w-completed { color: #059669; }

.member-popup-workload-item .w-label {
    display: block;
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 0.35rem;
}

/* ============================================ */
/* PRINCIPAL CROWN BADGE                        */
/* ============================================ */
.badge-principal-crown {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.7rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #fcd34d;
    white-space: nowrap;
}
</style>

<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Departments</h2>
            <p class="text-base text-gray-500 mt-1">{{ $org->name ?? 'Company' }}</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->isPrincipal())
                <a href="{{ route('departments.create') }}" class="btn-primary inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> New Department
                </a>
            @endif
        </div>
    </div>

    {{-- STATS (colorful KPI cards) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="kpi-card kpi-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Total Members</p>
                    <p class="kpi-value mt-1">{{ $members->count() }}</p>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Departments</p>
                    <p class="kpi-value mt-1">{{ $departments->count() }}</p>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Unassigned</p>
                    <p class="kpi-value mt-1">{{ $unassignedMembers->count() }}</p>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">In Departments</p>
                    <p class="kpi-value mt-1">{{ $departments->sum(fn($d) => $d->members->count()) }}</p>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN LAYOUT --}}
    <div class="departments-layout">
        {{-- LEFT: DEPARTMENTS GRID --}}
        <div>
            @if($departments->count() > 0)
                <div class="grid grid-cols-1 gap-6">
                    @foreach($departments as $department)
                        @php
                            $memberCount = $department->members->count();
                            $isPrincipal = auth()->user()->isPrincipal();
                        @endphp

                        <a href="{{ route('departments.show', $department) }}" class="dept-card-link">
                            <div class="dept-card">
                                {{-- DARK NAVY HEADER --}}
                                <div class="dept-header">
                                    <h4>{{ $department->name }}</h4>
                                    <span class="dept-status-badge {{ $department->status === 'active' ? 'active' : 'inactive' }}">
                                        <i class="fas fa-circle" style="font-size:0.4rem;"></i>
                                        {{ ucfirst($department->status) }}
                                    </span>
                                </div>

                                <div class="dept-body">
                                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                        <span class="flex items-center gap-1.5">
                                            <i class="fas fa-users text-blue-400"></i>
                                            {{ $memberCount }} members
                                        </span>
                                    </div>

                                    <div class="space-y-1.5 max-h-52 overflow-y-auto">
                                        @foreach($department->members->take(5) as $member)
                                            <div class="member-item">
                                                <img src="{{ $member->getProfilePictureUrl() }}"
                                                     alt="{{ $member->name }}"
                                                     class="w-9 h-9 rounded-full object-cover">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $member->name }}</p>
                                                    <p class="text-xs text-gray-400 truncate">
                                                        {{ $member->getJobScopeLabel() ?? 'No job scope' }}
                                                    </p>
                                                </div>

                                                @if($member->role === 'principal')
                                                    <span class="badge-principal-crown">
                                                        <i class="fas fa-crown"></i> Principal
                                                    </span>
                                                @endif

                                                <span class="w-2.5 h-2.5 rounded-full
                                                    @if($member->status === 'active') bg-green-500
                                                    @elseif($member->status === 'outstation') bg-blue-500
                                                    @elseif($member->status === 'annual_leave') bg-yellow-500
                                                    @else bg-red-500 @endif">
                                                </span>
                                            </div>
                                        @endforeach
                                        @if($memberCount > 5)
                                            <p class="text-sm text-gray-400 text-center py-1">+{{ $memberCount - 5 }} more</p>
                                        @endif
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2 no-card-link">
                                        <span class="flex-1 text-center text-sm px-4 py-2.5 bg-[#1a2a4a] text-white rounded-lg font-medium">
                                            <i class="fas fa-eye mr-1.5"></i> View Details
                                        </span>
                                        @if($isPrincipal)
                                            <a href="{{ route('departments.edit', $department) }}"
                                               onclick="event.stopPropagation()"
                                               class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('departments.destroy', $department) }}"
                                                  onclick="event.stopPropagation()"
                                                  onsubmit="return confirm('⚠️ Delete this department?\n\nThis will remove all members.\nThis action cannot be undone!')"
                                                  class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-500 rounded-lg transition">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-200 p-16 text-center">
                    <i class="fas fa-building text-5xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-400 mb-4">No departments created yet.</p>
                    @if(auth()->user()->isPrincipal())
                        <a href="{{ route('departments.create') }}" class="inline-block btn-primary">
                            <i class="fas fa-plus mr-1"></i> Create First Department
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- RIGHT: MEMBER SIDEBAR --}}
        <div>
            <div class="member-list-card">
                {{-- DARK NAVY SIDEBAR HEADER --}}
                <div class="member-list-header">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-white text-lg">
                            All Members
                            <span class="text-white/60 font-normal">({{ $members->count() }})</span>
                        </h3>
                    </div>
                    <p class="text-sm text-white/70 mt-1.5">
                        Hover over a member to see details
                    </p>
                </div>

                <div class="p-4 border-b border-gray-100">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text"
                               id="memberSearch"
                               placeholder="Search members..."
                               class="search-input w-full pl-10 pr-4 py-2.5 text-base border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition">
                    </div>
                </div>

                <div class="p-2 max-h-[600px] overflow-y-auto" id="memberList">
                    @foreach($members as $member)
                        @php
                            $memberData = [
                                'id' => $member->id,
                                'name' => $member->name,
                                'email' => $member->email,
                                'phone' => $member->phone,
                                'role' => ucfirst($member->role),
                                'job_scope' => $member->getJobScopeLabel(),
                                'job_scope_icon' => $member->getJobScopeIcon(),
                                'department' => $member->department ? $member->department->name : null,
                                'status' => $member->status,
                                'status_label' => $member->getStatusLabel(),
                                'status_icon' => $member->getStatusIcon(),
                                'status_note' => $member->status_note,
                                'status_until' => $member->status_until ? $member->status_until->format('d M Y') : null,
                                'specialties' => $member->specialties ?? [],
                                'profile_picture' => $member->getProfilePictureUrl(),
                                'is_principal' => $member->isPrincipal(),
                            ];

                            if (auth()->user()->isPrincipal()) {
                                $memberData['workload'] = [
                                    'total' => $member->getTaskCount(),
                                    'pending' => $member->getPendingTaskCount(),
                                    'completed' => $member->getCompletedTaskCount(),
                                ];

                                $ledProjects = \App\Models\Project::where('leader_id', $member->id)
                                    ->where('status', 'active')
                                    ->get(['id', 'name'])
                                    ->map(function($p) {
                                        return [
                                            'id'        => $p->id,
                                            'route_key' => $p->getRouteKey(),
                                            'name'      => $p->name,
                                        ];
                                    })
                                    ->toArray();

                                if (count($ledProjects) > 0) {
                                    $memberData['led_projects'] = $ledProjects;
                                }
                            }
                        @endphp
                        <div class="member-list-item"
                             data-name="{{ strtolower($member->name) }}"
                             data-member="{{ json_encode($memberData) }}">
                            <img src="{{ $member->getProfilePictureUrl() }}"
                                 alt="{{ $member->name }}"
                                 class="w-10 h-10 rounded-full object-cover border border-gray-200">
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-medium text-gray-900 truncate">{{ $member->name }}</p>
                                <p class="text-sm text-gray-400 truncate">{{ $member->email }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                @if($member->role === 'principal')
                                    <span class="badge-principal-crown">
                                        <i class="fas fa-crown"></i> Principal
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">
                                        {{ $member->department ? $member->department->name : 'Unassigned' }}
                                    </span>
                                @endif
                            </div>

                            @if(auth()->user()->isPrincipal() && !$member->department && !$member->isPrincipal())
                                <a href="{{ route('departments.assign.department', $member) }}"
                                   class="text-sm px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition"
                                   title="Assign to department">
                                    <i class="fas fa-user-plus"></i>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div id="noMemberResults" class="hidden text-center py-12">
                    <i class="fas fa-search text-3xl text-gray-300 mb-3 block"></i>
                    <p class="text-base text-gray-400">No members found</p>
                </div>

                @if($unassignedMembers->count() > 0)
                    <div class="p-4 border-t border-gray-200 bg-amber-50 rounded-b-2xl">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-amber-700 flex items-center gap-1.5">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ $unassignedMembers->count() }} member(s) unassigned
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MEMBER HOVER POPUP                           --}}
{{-- ============================================ --}}
<div id="memberPopup" class="member-popup">
    <div class="member-popup-inner">
        <div class="member-popup-header">
            <img id="popupAvatar" src="" alt="" class="member-popup-avatar">
            <div class="min-w-0 flex-1">
                <p id="popupName" class="member-popup-name"></p>
                <p id="popupEmailHeader" class="member-popup-email"></p>
            </div>
            <span id="popupStatusBadge" class="member-popup-status"></span>
        </div>

        <div class="member-popup-body">
            {{-- Contact: Email --}}
            <div class="member-popup-row" id="popupEmailRow">
                <span class="member-popup-label"><i class="fas fa-envelope"></i> Email</span>
                <span id="popupEmail" class="member-popup-value"></span>
            </div>

            {{-- Contact: Mobile --}}
            <div class="member-popup-row" id="popupPhoneRow">
                <span class="member-popup-label"><i class="fas fa-phone"></i> Mobile</span>
                <span id="popupPhone" class="member-popup-value"></span>
            </div>

            <div class="member-popup-row">
                <span class="member-popup-label"><i class="fas fa-user-tag"></i> Role</span>
                <span id="popupRole" class="member-popup-value"></span>
            </div>
            <div class="member-popup-row" id="popupJobScopeRow">
                <span class="member-popup-label"><i id="popupJobScopeIcon" class="fas fa-briefcase"></i> Job Scope</span>
                <span id="popupJobScope" class="member-popup-value"></span>
            </div>
            <div class="member-popup-row" id="popupDepartmentRow">
                <span class="member-popup-label"><i class="fas fa-building"></i> Department</span>
                <span id="popupDepartment" class="member-popup-value"></span>
            </div>
            <div class="member-popup-row" id="popupStatusUntilRow">
                <span class="member-popup-label"><i class="fas fa-calendar-alt"></i> Until</span>
                <span id="popupStatusUntil" class="member-popup-value"></span>
            </div>
            <div class="member-popup-row" id="popupStatusNoteRow">
                <span class="member-popup-label"><i class="fas fa-comment"></i> Note</span>
                <span id="popupStatusNote" class="member-popup-value"></span>
            </div>
            <div class="member-popup-row" id="popupSpecialtiesRow">
                <span class="member-popup-label"><i class="fas fa-tags"></i> Specialties</span>
                <div id="popupSpecialties" class="member-popup-tags"></div>
            </div>

            @if(auth()->user()->isPrincipal())
                <div class="member-popup-row" id="popupLeadsRow" style="display: none;">
                    <span class="member-popup-label"><i class="fas fa-crown"></i> Leads</span>
                    <div id="popupLeads" class="member-popup-tags"></div>
                </div>

                <div id="popupWorkloadSection">
                    <div class="member-popup-divider"></div>
                    <div class="member-popup-workload" id="popupWorkload">
                        <div class="member-popup-workload-title">
                            <i class="fas fa-chart-bar"></i> Workload
                        </div>
                        <div class="member-popup-workload-grid">
                            <div class="member-popup-workload-item">
                                <span class="w-total" id="popupWorkloadTotal">0</span>
                                <span class="w-label">Total</span>
                            </div>
                            <div class="member-popup-workload-item">
                                <span class="w-pending" id="popupWorkloadPending">0</span>
                                <span class="w-label">Pending</span>
                            </div>
                            <div class="member-popup-workload-item">
                                <span class="w-completed" id="popupWorkloadCompleted">0</span>
                                <span class="w-label">Done</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        /* ===== SEARCH ===== */
        const searchInput = document.getElementById('memberSearch');
        const memberItems = document.querySelectorAll('.member-list-item');
        const noResults = document.getElementById('noMemberResults');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                let visible = 0;

                memberItems.forEach(function(item) {
                    const name = item.dataset.name || '';
                    if (name.includes(query)) {
                        item.style.display = '';
                        visible++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                noResults.classList.toggle('hidden', visible > 0);
            });
        }

        /* ===== MEMBER HOVER POPUP ===== */
        const popup = document.getElementById('memberPopup');
        const popupAvatar = document.getElementById('popupAvatar');
        const popupName = document.getElementById('popupName');
        const popupEmailHeader = document.getElementById('popupEmailHeader');
        const popupEmailRow = document.getElementById('popupEmailRow');
        const popupEmail = document.getElementById('popupEmail');
        const popupPhoneRow = document.getElementById('popupPhoneRow');
        const popupPhone = document.getElementById('popupPhone');
        const popupStatusBadge = document.getElementById('popupStatusBadge');
        const popupRole = document.getElementById('popupRole');
        const popupJobScopeRow = document.getElementById('popupJobScopeRow');
        const popupJobScope = document.getElementById('popupJobScope');
        const popupJobScopeIcon = document.getElementById('popupJobScopeIcon');
        const popupDepartmentRow = document.getElementById('popupDepartmentRow');
        const popupDepartment = document.getElementById('popupDepartment');
        const popupStatusUntilRow = document.getElementById('popupStatusUntilRow');
        const popupStatusUntil = document.getElementById('popupStatusUntil');
        const popupStatusNoteRow = document.getElementById('popupStatusNoteRow');
        const popupStatusNote = document.getElementById('popupStatusNote');
        const popupSpecialtiesRow = document.getElementById('popupSpecialtiesRow');
        const popupSpecialties = document.getElementById('popupSpecialties');

        let hoverTimeout = null;

        function positionPopup(item) {
            const itemRect = item.getBoundingClientRect();
            const popupWidth = 380;
            const popupHeight = popup.offsetHeight || 400;
            const margin = 12;
            const viewportWidth = window.innerWidth;
            const viewportHeight = window.innerHeight;

            let left = itemRect.left - popupWidth - margin;
            if (left < margin) left = itemRect.right + margin;
            if (left + popupWidth > viewportWidth - margin) left = viewportWidth - popupWidth - margin;

            let top = itemRect.top;
            if (top + popupHeight > viewportHeight - margin) top = viewportHeight - popupHeight - margin;
            if (top < margin) top = margin;

            popup.style.left = left + 'px';
            popup.style.top = top + 'px';
        }

        function telHref(phone) {
            const cleaned = String(phone).replace(/[^\d+]/g, '');
            return 'tel:' + cleaned;
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function showPopup(item) {
            let data;
            try {
                data = JSON.parse(item.dataset.member);
            } catch (e) {
                console.error('Failed to parse member data:', e);
                return;
            }

            popupAvatar.src = data.profile_picture || '';
            popupName.textContent = data.name || '';
            popupEmailHeader.textContent = data.email || '';

            popupStatusBadge.innerHTML = '<i class="fas ' + (data.status_icon || 'fa-circle') + '"></i> ' + (data.status_label || '');
            popupStatusBadge.className = 'member-popup-status status-' + data.status;

            if (data.email) {
                popupEmail.innerHTML = '<a href="mailto:' + escapeHtml(data.email) + '">' + escapeHtml(data.email) + '</a>';
                popupEmailRow.style.display = '';
            } else {
                popupEmailRow.style.display = 'none';
            }

            if (data.phone) {
                popupPhone.innerHTML = '<a href="' + escapeHtml(telHref(data.phone)) + '">' + escapeHtml(data.phone) + '</a>';
                popupPhoneRow.style.display = '';
            } else {
                popupPhoneRow.style.display = 'none';
            }

            popupRole.textContent = data.role || '—';

            if (data.is_principal) {
                popupJobScopeRow.style.display = 'none';
                popupDepartmentRow.style.display = 'none';
            } else {
                popupJobScopeRow.style.display = '';
                popupDepartmentRow.style.display = '';
                popupJobScope.textContent = data.job_scope || '—';
                popupJobScopeIcon.className = 'fas ' + (data.job_scope_icon || 'fa-briefcase');
                popupDepartment.textContent = data.department || 'Unassigned';
            }

            if (data.status_until) {
                popupStatusUntil.textContent = data.status_until;
                popupStatusUntilRow.style.display = '';
            } else {
                popupStatusUntilRow.style.display = 'none';
            }

            if (data.status_note) {
                popupStatusNote.textContent = data.status_note;
                popupStatusNoteRow.style.display = '';
            } else {
                popupStatusNoteRow.style.display = 'none';
            }

            popupSpecialties.innerHTML = '';
            if (data.specialties && data.specialties.length > 0) {
                data.specialties.forEach(function(skill) {
                    const tag = document.createElement('span');
                    tag.className = 'member-popup-tag';
                    tag.textContent = skill;
                    popupSpecialties.appendChild(tag);
                });
                popupSpecialtiesRow.style.display = '';
            } else {
                popupSpecialtiesRow.style.display = 'none';
            }

            const popupLeadsRow = document.getElementById('popupLeadsRow');
            const popupLeads = document.getElementById('popupLeads');

            if (popupLeadsRow && popupLeads) {
                popupLeads.innerHTML = '';

                if (data.led_projects && data.led_projects.length > 0) {
                    data.led_projects.forEach(function(project) {
                        const tag = document.createElement('a');
                        tag.className = 'member-popup-lead-tag';
                        tag.href = '/projects/' + (project.route_key || project.id);
                        tag.innerHTML = '<i class="fas fa-folder mr-1"></i> ' + escapeHtml(project.name);
                        popupLeads.appendChild(tag);
                    });
                    popupLeadsRow.style.display = '';
                } else {
                    popupLeadsRow.style.display = 'none';
                }
            }

            const popupWorkloadSection = document.getElementById('popupWorkloadSection');
            if (popupWorkloadSection) {
                if (data.is_principal) {
                    popupWorkloadSection.style.display = 'none';
                } else {
                    popupWorkloadSection.style.display = '';
                    if (data.workload) {
                        document.getElementById('popupWorkloadTotal').textContent = data.workload.total || 0;
                        document.getElementById('popupWorkloadPending').textContent = data.workload.pending || 0;
                        document.getElementById('popupWorkloadCompleted').textContent = data.workload.completed || 0;
                    }
                }
            }

            positionPopup(item);
            requestAnimationFrame(function() {
                popup.classList.add('visible');
            });
        }

        function hidePopup() {
            popup.classList.remove('visible');
        }

        memberItems.forEach(function(item) {
            item.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(function() {
                    showPopup(item);
                }, 120);
            });

            item.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimeout);
                hidePopup();
            });
        });

        window.addEventListener('scroll', hidePopup, true);
        window.addEventListener('resize', hidePopup);
    });
</script>

@endsection