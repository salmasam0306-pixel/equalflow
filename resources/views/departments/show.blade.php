{{-- resources/views/departments/show.blade.php --}}
@extends('layouts.app')

@section('title', $department->name)

@section('content')

<style>
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
.kpi-emerald { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.kpi-amber   { background: linear-gradient(135deg, #d97706 0%, #b45309 100%); }

/* ============================================ */
/* SECTION CARD (dark navy header)              */
/* ============================================ */
.show-card {
    background: #ffffff;
    border-radius: 1.25rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
    overflow: hidden;
}
.show-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
}

.show-card-header {
    padding: 1.15rem 1.5rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    position: relative;
}
.show-card-header::before {
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
.show-card-header h3 {
    font-size: 1.05rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.show-card-header h3 .count {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 400;
}
.show-card-header > * {
    position: relative;
    z-index: 1;
}

.show-card-body {
    padding: 1.5rem;
}

/* ============================================ */
/* MEMBER ITEMS                                 */
/* ============================================ */
.member-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.9rem 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f3f4f6;
}
.member-item:hover { background: #f8fafc; }
.member-item:last-child { border-bottom: none; }

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
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    box-shadow: 0 4px 15px rgba(26, 42, 74, 0.3);
    transform: translateY(-1px);
    color: white;
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
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
}
.btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
    color: #374151;
}

/* Header action button (inside navy header) */
.header-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.85rem;
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}
.header-action:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* Member action buttons */
.member-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 0.5rem;
    transition: all 0.15s ease;
    border: none;
    cursor: pointer;
    text-decoration: none;
}
.member-action.edit   { background: #eff6ff; color: #2563eb; }
.member-action.edit:hover   { background: #dbeafe; color: #1d4ed8; }
.member-action.remove { background: #fef2f2; color: #dc2626; }
.member-action.remove:hover { background: #fee2e2; color: #b91c1c; }

/* ============================================ */
/* INFO ROWS                                    */
/* ============================================ */
.info-row {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.7rem 0;
    border-bottom: 1px solid #f3f4f6;
}
.info-row:last-child { border-bottom: none; }
.info-row .info-label {
    color: #6b7280;
    font-weight: 500;
    font-size: 0.85rem;
    min-width: 110px;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-shrink: 0;
}
.info-row .info-label i { color: #9ca3af; font-size: 0.75rem; }
.info-row .info-value {
    color: #1f2937;
    font-weight: 500;
    font-size: 0.9rem;
    word-break: break-word;
}

/* Status pill */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.7rem;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 600;
}
.status-pill.active {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
.status-pill.inactive {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}
</style>

<div class="max-w-4xl mx-auto px-4 py-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">{{ $department->name }}</h2>
            <p class="text-base text-gray-500 mt-1">
                {{ $department->organization->name ?? 'Company' }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('departments.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            @if(auth()->user()->isPrincipal())
                <a href="{{ route('departments.edit', $department) }}" class="btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
        </div>
    </div>

    {{-- STATS (colorful KPI cards) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="kpi-card kpi-blue">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Members</p>
                    <p class="kpi-value mt-1">{{ $department->members->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-users"></i></div>
            </div>
        </div>

        <div class="kpi-card kpi-indigo">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Leaders</p>
                    <p class="kpi-value mt-1">{{ $department->members->where('role', 'leader')->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-user-tie"></i></div>
            </div>
        </div>

        <div class="kpi-card kpi-emerald">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Active</p>
                    <p class="kpi-value mt-1">{{ $department->members->where('status', 'active')->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>

        <div class="kpi-card kpi-amber">
            <div class="flex items-center justify-between">
                <div>
                    <p class="kpi-label">Away</p>
                    <p class="kpi-value mt-1">{{ $department->members->where('status', '!=', 'active')->count() }}</p>
                </div>
                <div class="kpi-icon"><i class="fas fa-clock"></i></div>
            </div>
        </div>
    </div>

    {{-- DEPARTMENT INFO --}}
    <div class="show-card mb-6">
        <div class="show-card-header">
            <h3><i class="fas fa-info-circle"></i> Department Information</h3>
        </div>
        <div class="show-card-body">
            <div class="info-row">
                <span class="info-label"><i class="fas fa-tag"></i> Name</span>
                <span class="info-value">{{ $department->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-circle"></i> Status</span>
                <span class="info-value">
                    <span class="status-pill {{ $department->status === 'active' ? 'active' : 'inactive' }}">
                        {{ ucfirst($department->status) }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-align-left"></i> Description</span>
                <span class="info-value">{{ $department->description ?? 'No description' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-calendar-plus"></i> Created</span>
                <span class="info-value">{{ $department->created_at->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-user"></i> Created By</span>
                <span class="info-value">{{ $department->creator->name ?? 'Unknown' }}</span>
            </div>
        </div>
    </div>

    {{-- MEMBERS LIST --}}
    <div class="show-card">
        <div class="show-card-header">
            <h3>
                <i class="fas fa-users"></i> Members
                <span class="count">({{ $department->members->count() }})</span>
            </h3>
            @if(auth()->user()->isPrincipal())
                <button onclick="showAddMemberModal()" class="header-action">
                    <i class="fas fa-user-plus"></i> Add Member
                </button>
            @endif
        </div>

        <div class="show-card-body">
            @if($department->members->count() > 0)
                <div>
                    @foreach($department->members as $member)
                        <div class="member-item">
                            <img src="{{ $member->getProfilePictureUrl() }}"
                                 alt="{{ $member->name }}"
                                 class="w-12 h-12 rounded-full object-cover border-2 border-gray-200">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2.5">
                                    <p class="text-base font-medium text-gray-900">{{ $member->name }}</p>
                                    <span class="text-xs px-2.5 py-0.5 rounded-full
                                        @if($member->role === 'principal') bg-purple-100 text-purple-700
                                        @elseif($member->role === 'leader') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-500 @endif">
                                        {{ ucfirst($member->role) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-400 mt-0.5">{{ $member->email }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-sm text-gray-500">{{ $member->getJobScopeLabel() ?? 'No job scope' }}</span>
                                    <span class="w-2 h-2 rounded-full
                                        @if($member->status === 'active') bg-green-500
                                        @elseif($member->status === 'outstation') bg-blue-500
                                        @elseif($member->status === 'annual_leave') bg-yellow-500
                                        @else bg-red-500 @endif">
                                    </span>
                                    <span class="text-sm text-gray-500">{{ $member->getStatusLabel() }}</span>
                                </div>
                            </div>

                            @if(auth()->user()->isPrincipal() && $member->id !== auth()->id())
                                {{-- Edit: jump to the assign-department form, scoped to this department --}}
                                <a href="{{ route('departments.assign.department', ['user' => $member, 'from' => 'department', 'department_id' => $department->id]) }}"
                                   class="member-action edit"
                                   title="Edit department, job scope & skills">
                                    <i class="fas fa-pen"></i>
                                </a>

                                {{-- Remove from department --}}
                                <form method="POST"
                                      action="{{ route('departments.members.remove', ['department' => $department, 'user' => $member]) }}"
                                      onsubmit="return confirm('Remove {{ $member->name }} from this department?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="member-action remove" title="Remove from department">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users-slash text-5xl text-gray-300 mb-4 block"></i>
                    <p class="text-base text-gray-400">No members in this department yet.</p>
                    @if(auth()->user()->isPrincipal())
                        <p class="text-sm text-gray-400 mt-2">Click <strong>Add Member</strong> to assign members.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Add Member Modal --}}
    @if(auth()->user()->isPrincipal())
        <div id="addMemberModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                <div class="show-card-header">
                    <h3><i class="fas fa-user-plus"></i> Add Member</h3>
                    <button onclick="closeAddMemberModal()" class="text-white/80 hover:text-white transition text-lg leading-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('departments.members.add', $department) }}" class="p-6">
                    @csrf

                    <p class="text-sm text-gray-500 mb-5">Select a member to add to this department.</p>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Member
                        </label>
                        <select name="user_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition bg-white text-base">
                            <option value="">Select a member...</option>
                            @php
                                $availableMembers = \App\Models\User::whereHas('organizationMembers', function($q) use ($department) {
                                    $q->where('organization_id', $department->organization_id);
                                })->whereNull('department_id')->get();
                            @endphp
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 btn-primary justify-center">
                            <i class="fas fa-user-plus"></i> Add Member
                        </button>
                        <button type="button" onclick="closeAddMemberModal()" class="flex-1 btn-secondary justify-center">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function showAddMemberModal() {
                document.getElementById('addMemberModal').classList.remove('hidden');
            }
            function closeAddMemberModal() {
                document.getElementById('addMemberModal').classList.add('hidden');
            }
            document.getElementById('addMemberModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeAddMemberModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeAddMemberModal();
            });
        </script>
    @endif
</div>

@endsection