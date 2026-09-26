{{-- resources/views/tasks/show.blade.php --}}
@extends('layouts.app')

@section('title', $task->title)

@section('content')

<style>
.card {
    background: #fff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden;
}

/* ============================================ */
/* MEDIUM TINTED CARD                           */
/* ============================================ */
.card-tinted {
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 1rem;
    box-shadow: 0 1px 3px rgba(26, 42, 74, 0.06);
    position: relative;
    overflow: hidden;
}
.card-tinted::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 65%;
    height: 65%;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.card-tinted > * {
    position: relative;
    z-index: 1;
}
.card-tinted h3 {
    color: #1a2a4a;
}
.card-tinted .label-muted {
    color: #475569;
}
.card-tinted .value-bright {
    color: #0f1a30;
}

.card-tinted .pill-indigo {
    background: #c7d2fe;
    color: #312e81;
    border: 1px solid #a5b4fc;
}
.card-tinted .pill-emerald {
    background: #a7f3d0;
    color: #064e3b;
    border: 1px solid #6ee7b7;
}
.card-tinted .pill-amber {
    background: #fde68a;
    color: #78350f;
    border: 1px solid #fcd34d;
}
.card-tinted .divider-soft {
    border-color: #bfdbfe;
}

/* ============================================ */
/* SUBTASK ROW                                  */
/* ============================================ */
.subtask-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.15s ease;
    text-decoration: none;
    color: inherit;
}
.subtask-row:last-child { border-bottom: none; }
.subtask-row:hover { background: #f8fafc; }

/* ============================================ */
/* STATUS / PRIORITY PILLS                      */
/* ============================================ */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.85rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.status-todo         { background: #fef3c7; color: #92400e; }
.status-in_progress  { background: #dbeafe; color: #1e40af; }
.status-review       { background: #ede9fe; color: #5b21b6; }
.status-done         { background: #d1fae5; color: #065f46; }
.status-blocked      { background: #fecaca; color: #991b1b; }

.priority-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.7rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 600;
}
.priority-low    { background: #f3f4f6; color: #4b5563; }
.priority-medium { background: #e0f2fe; color: #075985; }
.priority-high   { background: #fee2e2; color: #991b1b; }

/* ============================================ */
/* BUTTONS                                      */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.65rem 1.25rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.85rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.2s ease;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(26, 42, 74, 0.25); color: #fff; }

.btn-secondary {
    background: #f3f4f6;
    color: #475569;
    padding: 0.65rem 1.25rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.85rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}
.btn-secondary:hover { background: #e5e7eb; color: #334155; }

/* ============================================ */
/* EDIT BUTTON (dark navy)                      */
/* ============================================ */
.btn-edit {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    padding: 0.65rem 1.25rem;
    border-radius: 0.65rem;
    font-weight: 600;
    font-size: 0.85rem;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(26, 42, 74, 0.2);
    position: relative;
    overflow: hidden;
}
.btn-edit:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.35);
    color: #ffffff;
}
.btn-edit::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}
.btn-edit:hover::before {
    left: 100%;
}

/* ============================================ */
/* PROGRESS BAR                                 */
/* ============================================ */
.progress-track {
    width: 100%;
    height: 8px;
    background: #ffffff;
    border: 1px solid #bfdbfe;
    border-radius: 9999px;
    overflow: hidden;
}
.progress-fill  { height: 100%; border-radius: 9999px; transition: width 0.5s ease; }
.progress-emerald { background: linear-gradient(90deg, #6ee7b7, #34d399); }
.progress-yellow  { background: linear-gradient(90deg, #fbbf24, #d97706); }
.progress-orange  { background: linear-gradient(90deg, #fb923c, #ea580c); }

/* ============================================ */
/* SUB-TASKS CARD HEADER (dark navy)            */
/* ============================================ */
.subtask-card-header {
    padding: 1.15rem 1.5rem;
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    position: relative;
    overflow: hidden;
}
.subtask-card-header::before {
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
.subtask-card-header h3 {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
    z-index: 1;
}
.subtask-card-header h3 .count {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 400;
}
.header-action {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.9rem;
    border-radius: 0.5rem;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #ffffff;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s ease;
    position: relative;
    z-index: 1;
}
.header-action:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}
</style>

<div class="max-w-5xl mx-auto px-4 py-6">

    @php
        $user = auth()->user();
        $isPrincipal = $user->isPrincipal();
        $isLeader = $user->id === $task->assigned_to;
        $canCreateSubtask = $isLeader;

        $subProgress = $task->subtask_progress;
        $progressColor = $subProgress['percent'] >= 100 ? 'emerald' : ($subProgress['percent'] >= 50 ? 'yellow' : 'orange');
    @endphp

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('projects.show', $task->project) }}" class="text-sm text-gray-400 hover:text-gray-600 inline-flex items-center gap-1 mb-2">
                <i class="fas fa-arrow-left"></i> {{ $task->project->name }}
            </a>
            <h2 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h2>
            @if($task->description)
                <p class="text-base text-gray-500 mt-2">{{ $task->description }}</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($canCreateSubtask)
                <a href="{{ route('tasks.subtask.create', $task) }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Add Sub-task
                </a>
            @endif
            @if($isPrincipal)
                <a href="{{ route('tasks.edit', $task) }}" class="btn-edit">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
        </div>
    </div>

    {{-- Status badges --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="status-pill status-{{ $task->status }}">
            <i class="fas {{ $task->getStatusIcon() }}"></i> {{ $task->getStatusLabel() }}
        </span>
        <span class="priority-pill priority-{{ $task->priority }}">
            <i class="fas fa-flag"></i> {{ $task->getPriorityLabel() }}
        </span>
        @if($task->department)
            <span class="text-sm px-3.5 py-1.5 rounded-full bg-indigo-50 text-indigo-700 font-medium inline-flex items-center gap-1.5">
                <i class="fas fa-building"></i> {{ $task->department->name }}
            </span>
        @endif
        @if($task->due_date)
            <span class="text-sm px-3.5 py-1.5 rounded-full bg-amber-50 text-amber-700 font-medium inline-flex items-center gap-1.5">
                <i class="fas fa-clock"></i> Due {{ $task->due_date->format('d M Y') }}
            </span>
        @endif
        @if($task->is_blocked)
            <span class="text-sm px-3.5 py-1.5 rounded-full bg-red-50 text-red-700 font-medium inline-flex items-center gap-1.5">
                <i class="fas fa-lock"></i> Blocked
            </span>
        @endif
    </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

        {{-- Task Details --}}
        <div class="lg:col-span-2 card-tinted p-6">
            <h3 class="text-base font-semibold mb-5">Task Details</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <span class="text-sm label-muted">Assigned Leader</span>
                    <div class="flex items-center gap-2.5 mt-1.5">
                        @if($task->assignee)
                            <img src="{{ $task->assignee->getProfilePictureUrl() }}" class="w-8 h-8 rounded-full object-cover border border-white">
                            <span class="font-medium value-bright">{{ $task->assignee->name }}</span>
                        @else
                            <span class="label-muted">Unassigned</span>
                        @endif
                    </div>
                </div>

                <div>
                    <span class="text-sm label-muted">Created By</span>
                    <p class="font-medium value-bright mt-1.5">{{ $task->creator->name ?? '—' }}</p>
                </div>

                <div>
                    <span class="text-sm label-muted">Start Date</span>
                    <p class="font-medium value-bright mt-1.5">{{ $task->start_date ? $task->start_date->format('d M Y') : '—' }}</p>
                </div>

                <div>
                    <span class="text-sm label-muted">Due Date</span>
                    <p class="font-medium value-bright mt-1.5">{{ $task->due_date ? $task->due_date->format('d M Y') : '—' }}</p>
                </div>
            </div>

            @if(!empty($task->skills_required))
                <div class="mt-6 pt-5 border-t divider-soft">
                    <span class="text-sm label-muted">Skills Required</span>
                    <div class="flex flex-wrap gap-2 mt-2.5">
                        @foreach($task->skills_required as $skill)
                            <span class="text-sm px-3 py-1 rounded-full pill-indigo">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($task->dependsOnTasks->count() > 0)
                <div class="mt-6 pt-5 border-t divider-soft">
                    <span class="text-sm label-muted">Depends On</span>
                    <div class="flex flex-wrap gap-2 mt-2.5">
                        @foreach($task->dependsOnTasks as $dep)
                            <a href="{{ route('tasks.show', $dep) }}"
                               class="text-sm px-3 py-1 rounded-full inline-flex items-center gap-1.5 transition
                                      {{ $dep->status === 'done' ? 'pill-emerald' : 'pill-amber' }}">
                                <i class="fas {{ $dep->status === 'done' ? 'fa-check' : 'fa-clock' }}"></i>
                                {{ $dep->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Progress --}}
        <div class="card-tinted p-6">
            <h3 class="text-base font-semibold mb-5">Progress</h3>

            <div class="text-center mb-5">
                <div class="text-5xl font-bold text-[#1a2a4a]">{{ $subProgress['percent'] }}%</div>
                <div class="text-sm label-muted mt-2">{{ $subProgress['done'] }} of {{ $subProgress['total'] }} sub-tasks</div>
            </div>

            <div class="progress-track mb-5">
                <div class="progress-fill progress-{{ $progressColor }}" style="width: {{ $subProgress['percent'] }}%"></div>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="label-muted">Sub-tasks</span>
                    <span class="font-semibold value-bright">{{ $subProgress['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="label-muted">Completed</span>
                    <span class="font-semibold text-emerald-700">{{ $subProgress['done'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="label-muted">Pending</span>
                    <span class="font-semibold text-amber-700">{{ $subProgress['total'] - $subProgress['done'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sub-tasks --}}
    <div class="card">
        {{-- DARK NAVY HEADER --}}
        <div class="subtask-card-header">
            <h3>
                <i class="fas fa-diagram-project"></i>
                Sub-tasks
                <span class="count">({{ $task->subtasks->count() }})</span>
            </h3>
            @if($canCreateSubtask)
                <a href="{{ route('tasks.subtask.create', $task) }}" class="header-action">
                    <i class="fas fa-plus"></i> Add
                </a>
            @endif
        </div>

        @forelse($task->subtasks as $sub)
            <a href="{{ route('tasks.subtask.show', $sub) }}" class="subtask-row">
                <img src="{{ $sub->assignee?->getProfilePictureUrl() ?? 'https://ui-avatars.com/api/?name=?&background=e5e7eb&color=9ca3af' }}"
                     class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0" alt="">

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <p class="text-base font-medium text-gray-900 truncate">{{ $sub->title }}</p>
                        <span class="priority-pill priority-{{ $sub->priority }}">
                            {{ $sub->getPriorityLabel() }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                        <span><i class="fas fa-user text-gray-400"></i> {{ $sub->assignee?->name ?? 'Unassigned' }}</span>
                        @if($sub->due_date)
                            <span><i class="fas fa-calendar text-gray-400"></i> {{ $sub->due_date->format('d M') }}</span>
                        @endif
                    </div>
                </div>

                <span class="status-pill status-{{ $sub->status }}">
                    <i class="fas {{ $sub->getStatusIcon() }}"></i> {{ $sub->getStatusLabel() }}
                </span>

                <i class="fas fa-chevron-right text-gray-300"></i>
            </a>
        @empty
            <div class="text-center py-14">
                <i class="fas fa-inbox text-5xl text-gray-200 mb-4 block"></i>
                <p class="text-gray-400 mb-4">No sub-tasks yet.</p>
                @if($canCreateSubtask)
                    <a href="{{ route('tasks.subtask.create', $task) }}" class="btn-primary">
                        <i class="fas fa-plus"></i> Create first sub-task
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</div>

@endsection