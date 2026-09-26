{{-- resources/views/projects/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Projects')

@section('content')

<style>
/* ============================================ */
/* PROJECT CARD                                 */
/* ============================================ */
.project-card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.25s ease;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.project-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
}

/* Vertical status line */
.project-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
    z-index: 10;
    transition: width 0.25s ease;
}

.project-card:hover::before {
    width: 5px;
}

.project-card.status-active::before    { background: linear-gradient(180deg, #34d399, #059669); }
.project-card.status-completed::before { background: linear-gradient(180deg, #60a5fa, #2563eb); }
.project-card.status-on-hold::before   { background: linear-gradient(180deg, #fcd34d, #d97706); }
.project-card.status-overdue::before   { background: linear-gradient(180deg, #f87171, #dc2626); }

/* ============================================ */
/* TITLE BAR WITH FILL                          */
/* ============================================ */
.title-bar {
    position: relative;
    padding: 0.9rem 1.15rem 0.9rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
    overflow: hidden;
}

.title-bar-fill {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    transition: width 0.9s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0.9;
}

.title-bar-fill.progress-red     { background: linear-gradient(90deg, #fca5a5, #f87171); }
.title-bar-fill.progress-orange  { background: linear-gradient(90deg, #fdba74, #fb923c); }
.title-bar-fill.progress-yellow  { background: linear-gradient(90deg, #fde68a, #fbbf24); }
.title-bar-fill.progress-emerald { background: linear-gradient(90deg, #6ee7b7, #34d399); }

.title-bar-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}

.title-bar-name {
    font-weight: 600;
    font-size: 0.875rem;
    color: #111827;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6);
    min-width: 0;
    flex: 1;
}

.title-bar-name i {
    color: #1a2a4a;
    margin-right: 0.35rem;
}

.title-bar-percent {
    font-size: 0.95rem;
    font-weight: 800;
    flex-shrink: 0;
    letter-spacing: -0.02em;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.7);
}

.title-bar-percent.red     { color: #b91c1c; }
.title-bar-percent.orange  { color: #c2410c; }
.title-bar-percent.yellow  { color: #b45309; }
.title-bar-percent.emerald { color: #047857; }

/* ============================================ */
/* BODY                                         */
/* ============================================ */
.project-body {
    padding: 1rem 1.25rem 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.progress-text {
    font-size: 0.7rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* ============================================ */
/* TIMELINE BAR                                 */
/* ============================================ */
.timeline-block {
    margin-bottom: 0.75rem;
}

.timeline-track {
    position: relative;
    width: 100%;
    height: 8px;
    background: #e2e8f0;
    border-radius: 9999px;
}

.timeline-fill {
    height: 100%;
    min-width: 6px;
    border-radius: 9999px;
    transition: width 0.9s cubic-bezier(0.4, 0, 0.2, 1);
}

.timeline-fill.timeline-red     { background: linear-gradient(90deg, #f87171, #dc2626); }
.timeline-fill.timeline-orange  { background: linear-gradient(90deg, #fb923c, #ea580c); }
.timeline-fill.timeline-yellow  { background: linear-gradient(90deg, #fbbf24, #d97706); }
.timeline-fill.timeline-emerald { background: linear-gradient(90deg, #34d399, #059669); }
.timeline-fill.timeline-slate   { background: linear-gradient(90deg, #94a3b8, #64748b); }

.timeline-dot {
    position: absolute;
    top: 50%;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    border: 3px solid #ffffff;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.05), 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: left 0.9s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 2;
}

.timeline-dot.timeline-dot-red     { background: #dc2626; }
.timeline-dot.timeline-dot-orange  { background: #ea580c; }
.timeline-dot.timeline-dot-yellow  { background: #d97706; }
.timeline-dot.timeline-dot-emerald { background: #059669; }
.timeline-dot.timeline-dot-slate   { background: #64748b; }

/* ============================================ */
/* BUTTONS                                      */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.25s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    transform: translateY(-1px);
}

/* ============================================ */
/* EMPTY STATE                                  */
/* ============================================ */
.empty-state {
    background: #ffffff;
    border-radius: 1.5rem;
    border: 1px solid #e5e7eb;
    padding: 3rem;
    text-align: center;
}

.empty-state .empty-icon {
    font-size: 3.5rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}
</style>

<div class="max-w-7xl mx-auto px-4">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-sm">
                    <i class="fas fa-folder-open"></i>
                </span>
                Company Projects
            </h2>
            <p class="text-sm text-gray-500">Manage all your company projects</p>
        </div>
        @if(auth()->user()->isPrincipal())
            <a href="{{ route('projects.create') }}" class="btn-primary inline-flex items-center gap-2">
                <i class="fas fa-plus-circle"></i> New Project
            </a>
        @endif
    </div>

    {{-- PROJECTS GRID --}}
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($projects as $project)
                @php
                    $isPrincipal = auth()->user()->isPrincipal();

                    /* ===== TASK PROGRESS (matches projects.show) ===== */
                    // Prefer sub-task progress; fall back to main-task progress if no subs exist.
                    // This prevents main tasks from being double-counted alongside their sub-tasks.
                    $totalSubs = $project->tasks()->where('task_type', 'sub')->count();
                    $doneSubs  = $project->tasks()->where('task_type', 'sub')->where('status', 'done')->count();

                    if ($totalSubs > 0) {
                        $totalTasks = $totalSubs;
                        $doneTasks  = $doneSubs;
                        $progressLabel = 'sub-tasks';
                    } else {
                        $totalTasks = $project->tasks()->where('task_type', 'main')->count();
                        $doneTasks  = $project->tasks()->where('task_type', 'main')->where('status', 'done')->count();
                        $progressLabel = 'main tasks';
                    }

                    $progress = $totalTasks > 0 ? (int) round(($doneTasks / $totalTasks) * 100) : 0;

                    if ($project->is_overdue && $progress < 100) {
                        $progressColor = 'red';
                    } elseif ($progress >= 100) {
                        $progressColor = 'emerald';
                    } elseif ($progress >= 50) {
                        $progressColor = 'yellow';
                    } else {
                        $progressColor = 'orange';
                    }

                    /* ===== STATUS LINE ===== */
                    if ($project->is_overdue && $project->status !== 'completed') {
                        $statusClass = 'status-overdue';
                    } elseif ($project->status === 'completed') {
                        $statusClass = 'status-completed';
                    } elseif ($project->status === 'on_hold') {
                        $statusClass = 'status-on-hold';
                    } else {
                        $statusClass = 'status-active';
                    }

                    /* ===== TIMELINE ===== */
                    $timelinePercent = 0;
                    $timelineColor   = 'slate';
                    $timelineLabel   = 'Not started';

                    if ($project->start_date && $project->deadline && $project->duration_days > 0) {
                        $elapsed = max(0, min($project->days_elapsed, $project->duration_days));
                        $timelinePercent = (int) round(($elapsed / $project->duration_days) * 100);

                        if ($project->is_overdue) {
                            $timelineColor = 'red';
                            $timelineLabel = 'Overdue by ' . abs($project->days_until_deadline) . 'd';
                        } else {
                            $daysLeft = $project->days_until_deadline;

                            if ($daysLeft <= 0) {
                                $timelineColor = 'red';
                                $timelineLabel = 'Due today';
                            } elseif ($daysLeft <= 7) {
                                $timelineColor = 'orange';
                                $timelineLabel = $daysLeft . 'd left';
                            } elseif ($daysLeft <= 30) {
                                $timelineColor = 'yellow';
                                $timelineLabel = $daysLeft . 'd left';
                            } else {
                                $timelineColor = 'emerald';
                                $timelineLabel = $daysLeft . 'd left';
                            }
                        }
                    } elseif ($project->start_date && !$project->deadline) {
                        $timelineLabel = 'Started ' . $project->start_date->format('d M');
                    } elseif (!$project->start_date && $project->deadline) {
                        $timelineLabel = 'Due ' . $project->deadline->format('d M');
                    }
                @endphp

                <div class="project-card {{ $statusClass }}">
                    {{-- TITLE BAR --}}
                    <div class="title-bar">
                        <div class="title-bar-fill progress-{{ $progressColor }}"
                             style="width: {{ $progress }}%"></div>

                        <div class="title-bar-content">
                            <h3 class="title-bar-name">
                                <i class="fas fa-folder"></i>{{ $project->name }}
                            </h3>
                            <span class="title-bar-percent {{ $progressColor }}">
                                {{ $progress }}%
                            </span>
                        </div>
                    </div>

                    {{-- BODY --}}
                    <div class="project-body">
                        @if($project->description)
                            <p class="text-xs text-gray-500 mb-1.5 line-clamp-2">
                                {{ Str::limit($project->description, 80) }}
                            </p>
                        @endif

                        <div class="progress-text">
                            <i class="fas fa-tasks text-indigo-400"></i>
                            <span>{{ $doneTasks }} of {{ $totalTasks }} {{ $progressLabel }} done</span>
                        </div>

                        {{-- Timeline --}}
                        @if($project->start_date || $project->deadline)
                            <div class="timeline-block">
                                <div class="flex items-center justify-between text-[0.65rem] text-gray-500 mb-1.5">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-flag-checkered text-rose-400"></i>
                                        <span class="font-medium">Timeline</span>
                                    </span>
                                    <span class="font-semibold text-{{ $timelineColor }}-600">
                                        {{ $timelineLabel }}
                                    </span>
                                </div>

                                <div class="timeline-track">
                                    <div class="timeline-fill timeline-{{ $timelineColor }}"
                                         style="width: {{ max($timelinePercent, 2) }}%"></div>
                                    @if($timelinePercent > 0 && $timelinePercent < 100)
                                        <div class="timeline-dot timeline-dot-{{ $timelineColor }}"
                                             style="left: {{ $timelinePercent }}%"></div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-[0.6rem] text-gray-400 mt-1">
                                    <span>{{ $project->start_date ? $project->start_date->format('d M Y') : '—' }}</span>
                                    <span>{{ $project->deadline ? $project->deadline->format('d M Y') : '—' }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- ACTIONS --}}
                        <div class="mt-auto flex gap-2 pt-3 border-t border-gray-100">
                            <a href="{{ route('projects.show', $project) }}"
                               class="flex-1 text-center px-3 py-2 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white text-sm font-medium rounded-lg transition inline-flex items-center justify-center gap-1">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if($isPrincipal)
                                <a href="{{ route('projects.edit', $project) }}"
                                   class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm rounded-lg transition"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('projects.destroy', $project) }}"
                                      onsubmit="return confirm('⚠️ Delete this project?\n\nThis will permanently delete all tasks and data.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-500 text-sm rounded-lg transition"
                                            title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <p class="text-gray-400 text-sm mb-4">No company projects yet.</p>
            @if(auth()->user()->isPrincipal())
                <a href="{{ route('projects.create') }}" class="btn-primary text-sm inline-flex items-center gap-1">
                    <i class="fas fa-plus"></i> Create your first project
                </a>
            @endif
        </div>
    @endif
</div>

@endsection