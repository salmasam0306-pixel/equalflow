{{-- resources/views/projects/show.blade.php --}}
@extends('layouts.app')

@section('title', $project->name)

@section('content')

<style>
/* ===== CARD ===== */
.card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* ===== TWO-COLUMN LAYOUT ===== */
.page-layout {
    display: grid;
    grid-template-columns: 1fr 200px;
    gap: 1.25rem;
    align-items: start;
}

@media (max-width: 1024px) {
    .page-layout {
        grid-template-columns: 1fr;
    }
}

/* ===== VERTICAL PROGRESS SIDEBAR (right) ===== */
.progress-sidebar {
    position: sticky;
    top: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 1.25rem 1rem;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.vbar-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.vbar-track {
    position: relative;
    width: 20px;
    height: 200px;
    background: #f1f5f9;
    border-radius: 9999px;
    overflow: hidden;
    display: flex;
    flex-direction: column-reverse;
}

.vbar-fill {
    width: 100%;
    border-radius: 9999px;
    transition: height 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.vbar-fill.progress-red     { background: linear-gradient(0deg, #f87171, #dc2626); }
.vbar-fill.progress-orange  { background: linear-gradient(0deg, #fb923c, #ea580c); }
.vbar-fill.progress-yellow  { background: linear-gradient(0deg, #fbbf24, #d97706); }
.vbar-fill.progress-emerald { background: linear-gradient(0deg, #34d399, #059669); }

.vbar-pct {
    font-size: 1.25rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1;
}

.vbar-pct.red     { color: #b91c1c; }
.vbar-pct.orange  { color: #c2410c; }
.vbar-pct.yellow  { color: #b45309; }
.vbar-pct.emerald { color: #047857; }

.vbar-label {
    font-size: 0.55rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #94a3b8;
    text-align: center;
    line-height: 1.3;
}

.sidebar-block {
    width: 100%;
    text-align: center;
    padding-top: 0.9rem;
    border-top: 1px solid #f3f4f6;
}

.sidebar-block-label {
    font-size: 0.55rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #94a3b8;
    margin-bottom: 0.2rem;
}

.sidebar-block-value {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
}

/* ===== TASK ROW ===== */
.task-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.9rem 1.15rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.15s ease;
    text-decoration: none;
    color: inherit;
}
.task-row:last-child { border-bottom: none; }
.task-row:hover { background: #f8fafc; }

.task-info { flex: 1; min-width: 0; }
.task-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 0.4rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.task-meta {
    font-size: 0.7rem;
    color: #9ca3af;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 0.2rem;
}

.sub-mini-track {
    width: 100px;
    height: 4px;
    background: #f1f5f9;
    border-radius: 9999px;
    overflow: hidden;
    flex-shrink: 0;
}
.sub-mini-fill { height: 100%; border-radius: 9999px; }
.sub-mini-fill.progress-emerald { background: #10b981; }
.sub-mini-fill.progress-yellow  { background: #f59e0b; }
.sub-mini-fill.progress-orange  { background: #f97316; }
.sub-mini-fill.progress-red     { background: #ef4444; }

.sub-mini-pct {
    font-size: 0.7rem;
    font-weight: 600;
    color: #6b7280;
    width: 36px;
    text-align: right;
    flex-shrink: 0;
}

/* ===== DEPARTMENT HEADER (dark navy) ===== */
.dept-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.5rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    font-size: 0.8rem;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}
.dept-bar::before {
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
.dept-bar-left {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    position: relative;
    z-index: 1;
}
.dept-bar-left i {
    color: rgba(255, 255, 255, 0.75);
}
.dept-pct {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 0.2rem 0.7rem;
    border-radius: 9999px;
    position: relative;
    z-index: 1;
}

/* ===== BUTTONS ===== */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    transition: all 0.25s ease;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    transform: translateY(-1px);
    color: white;
}
.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    transition: all 0.25s ease;
}
.btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
    color: #374151;
}
</style>

<div class="max-w-6xl mx-auto px-4 py-6">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-folder text-[#1a2a4a]"></i>
                {{ $project->name }}
            </h2>
            @if($project->description)
                <p class="text-sm text-gray-500 mt-1">{{ $project->description }}</p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($project->status === 'completed')
                <a href="{{ route('projects.report', $project) }}" class="btn-secondary text-sm">
                    <i class="fas fa-file-alt"></i> View Report
                </a>
            @endif

            @if(auth()->user()->isPrincipal())
                <a href="{{ route('tasks.create', $project) }}" class="btn-primary text-sm">
                    <i class="fas fa-plus"></i> New Main Task
                </a>
            @endif

            <a href="{{ route('projects.index') }}" class="btn-secondary text-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @php
        $allTasks = $project->tasks;

        $mainTasks = $allTasks->where('task_type', 'main')->values();
        $subTasks  = $allTasks->where('task_type', 'sub')->values();

        $subsByParent = $subTasks->groupBy('parent_task_id');

        $totalForProgress = $subTasks->count() > 0 ? $subTasks->count() : $mainTasks->count();
        $doneForProgress  = $subTasks->count() > 0
            ? $subTasks->where('status', 'done')->count()
            : $mainTasks->where('status', 'done')->count();
        $progress = $totalForProgress > 0
            ? round(($doneForProgress / $totalForProgress) * 100)
            : 0;

        if ($project->is_overdue && $progress < 100) {
            $progressColor = 'red';
        } elseif ($progress >= 100) {
            $progressColor = 'emerald';
        } elseif ($progress >= 50) {
            $progressColor = 'yellow';
        } else {
            $progressColor = 'orange';
        }
    @endphp

    {{-- TWO-COLUMN PAGE --}}
    <div class="page-layout">

        {{-- LEFT: Main tasks grouped by department --}}
        <div>
            @php
                $grouped = $mainTasks->groupBy(fn ($t) => $t->department_id ?? 0);

                $sortedGroups = $grouped->sortKeys();

                $deptIds = $sortedGroups->keys()->filter(fn ($k) => $k !== 0)->all();
                $deptNames = \App\Models\Department::whereIn('id', $deptIds)->pluck('name', 'id');
            @endphp

            @forelse($sortedGroups as $deptId => $deptMainTasks)
                @php
                    $isStandalone = $deptId === 0;
                    $deptName = $isStandalone
                        ? 'Standalone'
                        : ($deptNames[$deptId] ?? 'Unknown');

                    $deptMainIds = $deptMainTasks->pluck('id')->all();
                    $deptSubs = $subTasks->whereIn('parent_task_id', $deptMainIds);

                    $deptTotal = $deptSubs->count() > 0 ? $deptSubs->count() : $deptMainTasks->count();
                    $deptDone  = $deptSubs->count() > 0
                        ? $deptSubs->where('status', 'done')->count()
                        : $deptMainTasks->where('status', 'done')->count();
                    $deptPct = $deptTotal > 0 ? round(($deptDone / $deptTotal) * 100) : 0;
                @endphp

                <div class="card mb-4">
                    {{-- DARK NAVY DEPARTMENT HEADER --}}
                    <div class="dept-bar">
                        <div class="dept-bar-left">
                            <i class="fas fa-{{ $isStandalone ? 'inbox' : 'building' }}"></i>
                            {{ $deptName }}
                        </div>
                        <span class="dept-pct">{{ $deptPct }}%</span>
                    </div>

                    @foreach($deptMainTasks as $mainTask)
                        @php
                            $subs = $subsByParent->get($mainTask->id, collect());
                            $subTotal = $subs->count();
                            $subDone  = $subs->where('status', 'done')->count();
                            $subPct   = $subTotal > 0 ? round(($subDone / $subTotal) * 100) : 0;

                            $subColor = $subPct >= 100 ? 'emerald' : ($subPct >= 50 ? 'yellow' : 'orange');
                        @endphp

                        <a href="{{ route('tasks.show', $mainTask) }}" class="task-row">
                            <div class="task-info">
                                <p class="task-title">
                                    <i class="fas fa-clipboard-list text-indigo-500"></i>
                                    {{ $mainTask->title }}
                                </p>
                                <div class="task-meta">
                                    @if($mainTask->assignee)
                                        <span><i class="fas fa-user-tie text-blue-400"></i> {{ $mainTask->assignee->name }}</span>
                                    @else
                                        <span class="text-amber-600"><i class="fas fa-exclamation-triangle"></i> No leader</span>
                                    @endif
                                    @if($mainTask->due_date)
                                        <span><i class="fas fa-flag-checkered text-rose-400"></i> {{ $mainTask->due_date->format('d M') }}</span>
                                    @endif
                                    @if($subTotal > 0)
                                        <span><i class="fas fa-list-check text-gray-400"></i> {{ $subDone }}/{{ $subTotal }} sub-tasks</span>
                                    @else
                                        <span class="text-gray-400"><i class="fas fa-inbox"></i> No sub-tasks yet</span>
                                    @endif
                                </div>
                            </div>

                            <div class="sub-mini-track">
                                <div class="sub-mini-fill progress-{{ $subColor }}" style="width: {{ $subPct }}%"></div>
                            </div>
                            <span class="sub-mini-pct">{{ $subPct }}%</span>

                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </a>
                    @endforeach
                </div>
            @empty
                <div class="card p-12 text-center">
                    <div class="text-4xl mb-3 text-gray-300">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <p class="text-gray-400 text-sm">No main tasks yet.</p>
                    @if(auth()->user()->isPrincipal())
                        <a href="{{ route('tasks.create', $project) }}" class="btn-primary text-sm mt-4 inline-flex">
                            <i class="fas fa-plus"></i> Create first main task
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- RIGHT: Progress sidebar (original white card) --}}
        <div class="progress-sidebar">
            <div class="vbar-wrap">
                <div class="vbar-track">
                    <div class="vbar-fill progress-{{ $progressColor }}" style="height: {{ $progress }}%"></div>
                </div>
                <div class="vbar-pct {{ $progressColor }}">{{ $progress }}%</div>
                <div class="vbar-label">Overall<br>Progress</div>
            </div>

            <div class="sidebar-block">
                <div class="sidebar-block-label">Sub-tasks</div>
                <div class="sidebar-block-value">
                    {{ $subTasks->where('status', 'done')->count() }} / {{ $subTasks->count() }}
                </div>
            </div>

            <div class="sidebar-block">
                <div class="sidebar-block-label">Main Tasks</div>
                <div class="sidebar-block-value">
                    {{ $mainTasks->count() }}
                </div>
            </div>

            @if($project->deadline)
                <div class="sidebar-block">
                    <div class="sidebar-block-label">Deadline</div>
                    <div class="sidebar-block-value">
                        {{ $project->deadline->format('d M Y') }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection