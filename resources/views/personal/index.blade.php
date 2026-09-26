@extends('layouts.app')

@section('title', 'Personal Projects')

@section('content')

<style>
/* ============================================ */
/* KPI CARDS (colorful top tiles)               */
/* ============================================ */
.kpi-card {
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
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
/* PROJECT CARD (navy header, tinted body)      */
/* ============================================ */
.project-card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.25s ease;
    overflow: hidden;
    position: relative;
}
.project-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 30px rgba(26, 42, 74, 0.12);
    border-color: #bfdbfe;
}

/* Dark navy header */
.project-card-header {
    padding: 0.85rem 1.25rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    position: relative;
    overflow: hidden;
}
.project-card-header::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -10%;
    width: 45%;
    height: 90%;
    background: rgba(255, 255, 255, 0.04);
    border-radius: 50%;
    pointer-events: none;
}
.project-card-header h3 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    position: relative;
    z-index: 1;
}
.project-card-header .status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 600;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}
.status-pill.active {
    background: rgba(34, 197, 94, 0.25);
    color: #d1fae5;
    border: 1px solid rgba(34, 197, 94, 0.4);
}
.status-pill.on_hold {
    background: rgba(234, 179, 8, 0.25);
    color: #fef3c7;
    border: 1px solid rgba(234, 179, 8, 0.4);
}
.status-pill.completed {
    background: rgba(59, 130, 246, 0.25);
    color: #dbeafe;
    border: 1px solid rgba(59, 130, 246, 0.4);
}

/* Body — soft blue tint */
.project-card-body {
    padding: 1rem 1.25rem 1.25rem;
    background: linear-gradient(180deg, #f8fbff 0%, #eff6ff 100%);
}

.project-card-description {
    font-size: 0.8rem;
    color: #475569;
    line-height: 1.45;
    margin-bottom: 0.85rem;
    min-height: 2.3rem;
}

/* Stats inline row */
.project-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.85rem;
}
.project-stat {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.7rem;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid #bfdbfe;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
    color: #1a2a4a;
}
.project-stat i { font-size: 0.7rem; }
.project-stat.tasks      i { color: #8b5cf6; }
.project-stat.deadline   i { color: #d97706; }
.project-stat.members    i { color: #2563eb; }

/* Invite code row */
.invite-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.55rem 0.75rem;
    background: rgba(255, 255, 255, 0.85);
    border: 1px dashed #bfdbfe;
    border-radius: 0.6rem;
    margin-bottom: 0.85rem;
}
.invite-row .invite-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}
.invite-row .invite-code {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    color: #1a2a4a;
    letter-spacing: 0.08em;
    font-size: 0.8rem;
}
.invite-row .copy-btn {
    background: none;
    border: none;
    color: #1a2a4a;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.15rem 0.4rem;
    border-radius: 0.35rem;
    transition: background 0.15s ease;
}
.invite-row .copy-btn:hover { background: #dbeafe; }

/* Actions */
.project-actions {
    display: flex;
    gap: 0.4rem;
    padding-top: 0.85rem;
    border-top: 1px solid rgba(191, 219, 254, 0.6);
}
.project-actions .btn-view {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.55rem 0.9rem;
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 0.6rem;
    text-decoration: none;
    transition: all 0.15s ease;
}
.project-actions .btn-view:hover {
    box-shadow: 0 4px 12px rgba(26, 42, 74, 0.28);
    transform: translateY(-1px);
    color: #ffffff;
}
.project-actions .btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 0.6rem;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    font-size: 0.8rem;
}
.project-actions .btn-invite {
    background: #dbeafe;
    color: #1e40af;
}
.project-actions .btn-invite:hover {
    background: #bfdbfe;
    transform: translateY(-1px);
}
.project-actions .btn-delete {
    background: #fee2e2;
    color: #991b1b;
}
.project-actions .btn-delete:hover {
    background: #fecaca;
    transform: translateY(-1px);
}

/* "Invited" badge — shows on cards the user is a member of (not owner) */
.member-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

/* ============================================ */
/* EMPTY STATE                                  */
/* ============================================ */
.empty-state {
    background: #ffffff;
    border-radius: 1.5rem;
    border: 1px solid #e5e7eb;
    padding: 3rem 2rem;
    text-align: center;
}
.empty-state .empty-icon {
    font-size: 4rem;
    color: #cbd5e1;
    margin-bottom: 1rem;
}

/* ============================================ */
/* TOP BUTTON                                   */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.6rem 1.25rem;
    border-radius: 0.7rem;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.2s ease;
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

/* ============================================ */
/* RESPONSIVE                                   */
/* ============================================ */
@media (max-width: 640px) {
    .kpi-card { padding: 1rem; }
    .kpi-card .kpi-value { font-size: 1.5rem; }
    .kpi-card .kpi-icon { width: 38px; height: 38px; font-size: 1rem; }
    .empty-state { padding: 2rem 1rem; }
    .empty-state .empty-icon { font-size: 3rem; }
}

/* Line clamp */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Toast animation */
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.fixed { animation: slideUp 0.3s ease-out; }
</style>

@php
    // Merge owned + invited projects into a single collection so the
    // rest of the view (KPIs, grid, empty state) works uniformly.
    $allProjects = $projects->concat($invitedProjects ?? collect())->unique('id');

    $totalProjects     = $allProjects->count();
    $activeProjects    = $allProjects->where('status', 'active')->count();
    $onHoldProjects    = $allProjects->where('status', 'on_hold')->count();
    $completedProjects = $allProjects->where('status', 'completed')->count();
@endphp

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm">
                    <i class="fas fa-user-circle"></i>
                </span>
                Personal Projects
            </h2>
            <p class="text-sm text-gray-500 flex items-center gap-1">
                <i class="fas fa-user text-gray-400"></i> Your private projects outside of work
            </p>
        </div>
        <a href="{{ route('personal.create') }}" class="btn-primary">
            <i class="fas fa-plus-circle"></i> New Project
        </a>
    </div>

    {{-- KPI CARDS --}}
    @if($totalProjects > 0)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="kpi-card kpi-blue">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="kpi-label">Total</p>
                        <p class="kpi-value mt-1">{{ $totalProjects }}</p>
                    </div>
                    <div class="kpi-icon"><i class="fas fa-folder"></i></div>
                </div>
            </div>

            <div class="kpi-card kpi-emerald">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="kpi-label">Active</p>
                        <p class="kpi-value mt-1">{{ $activeProjects }}</p>
                    </div>
                    <div class="kpi-icon"><i class="fas fa-play-circle"></i></div>
                </div>
            </div>

            <div class="kpi-card kpi-amber">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="kpi-label">On Hold</p>
                        <p class="kpi-value mt-1">{{ $onHoldProjects }}</p>
                    </div>
                    <div class="kpi-icon"><i class="fas fa-pause-circle"></i></div>
                </div>
            </div>

            <div class="kpi-card kpi-indigo">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="kpi-label">Completed</p>
                        <p class="kpi-value mt-1">{{ $completedProjects }}</p>
                    </div>
                    <div class="kpi-icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>
        </div>
    @endif

    {{-- PROJECTS GRID --}}
    @if($allProjects->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <p class="text-gray-400 text-sm">No personal projects yet.</p>
            <a href="{{ route('personal.create') }}" class="inline-block mt-4 btn-primary">
                <i class="fas fa-plus"></i> Create your first personal project
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($allProjects as $project)
                @php
                    $isOwner = (int) $project->created_by === (int) auth()->id();

                    $statusClass = $project->status === 'active'
                        ? 'active'
                        : ($project->status === 'on_hold' ? 'on_hold' : 'completed');
                    $statusIcon = match($project->status) {
                        'active'    => 'fa-play-circle',
                        'on_hold'   => 'fa-pause-circle',
                        'completed' => 'fa-check-circle',
                        default     => 'fa-circle',
                    };
                @endphp

                <div class="project-card">

                    {{-- DARK NAVY HEADER --}}
                    <div class="project-card-header">
                        <h3>
                            <i class="fas fa-folder"></i>
                            {{ $project->name }}
                        </h3>
                        <span class="status-pill {{ $statusClass }}">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>

                    {{-- TINTED BODY --}}
                    <div class="project-card-body">

                        {{-- Description --}}
                        <p class="project-card-description line-clamp-2">
                            {{ $project->description ?: 'No description provided.' }}
                        </p>

                        {{-- Stats --}}
                        <div class="project-stats">
                            <span class="project-stat tasks">
                                <i class="fas fa-tasks"></i>
                                {{ $project->tasks_count }} tasks
                            </span>
                            @if($project->deadline)
                                <span class="project-stat deadline">
                                    <i class="fas fa-clock"></i>
                                    {{ $project->deadline->format('d M Y') }}
                                </span>
                            @endif
                            <span class="project-stat members">
                                <i class="fas fa-users"></i>
                                {{ $project->members->count() }} members
                            </span>
                        </div>

                        {{-- "Invited" badge for non-owners --}}
                        @if(!$isOwner)
                            <div class="mb-2">
                                <span class="member-badge">
                                    <i class="fas fa-user-check"></i> You're a member
                                </span>
                            </div>
                        @endif

                        {{-- Invite Code (only show to the owner) --}}
                        @if($isOwner && $project->invite_code)
                            <div class="invite-row">
                                <div>
                                    <div class="invite-label">
                                        <i class="fas fa-key text-amber-500"></i>
                                        Invite Code
                                    </div>
                                    <div class="invite-code">{{ $project->invite_code }}</div>
                                </div>
                                <button type="button"
                                        onclick="copyToClipboard('{{ $project->invite_code }}')"
                                        class="copy-btn"
                                        title="Copy invite code">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="project-actions">
                            <a href="{{ route('personal.show', $project) }}" class="btn-view">
                                <i class="fas fa-eye"></i> View Project
                            </a>

                            @if($isOwner)
                                <a href="{{ route('personal.invite', $project) }}"
                                   class="btn-icon btn-invite"
                                   title="Invite members">
                                    <i class="fas fa-user-plus"></i>
                                </a>
                                <form method="POST" action="{{ route('personal.destroy', $project) }}"
                                      onsubmit="return confirm('⚠️ Delete this personal project?\n\n{{ $project->name }}\nThis will delete all tasks and data.\nThis action cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn-icon btn-delete"
                                            title="Delete project">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ============================================ --}}
{{-- SCRIPTS                                     --}}
{{-- ============================================ --}}
<script>
function copyToClipboard(text) {
    if (!text) {
        showToast('No invite code available', 'error');
        return;
    }

    navigator.clipboard.writeText(text).then(function() {
        showToast('Invite code copied!', 'success');
    }).catch(function() {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('Invite code copied!', 'success');
    });
}

function showToast(message, type = 'success') {
    const colors = {
        success: 'bg-emerald-500',
        error: 'bg-red-500',
        warning: 'bg-amber-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.className = `fixed bottom-6 right-6 ${colors[type] || 'bg-emerald-500'} text-white px-4 py-3 rounded-xl shadow-lg text-sm z-50 transition-opacity duration-300 flex items-center gap-3 max-w-sm`;
    toast.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle'} text-white/80"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}
</script>

@endsection