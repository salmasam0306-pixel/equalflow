@extends('layouts.app')

@section('title', $project->name)

@section('content')

<style>
/* ============================================ */
/* MEDIUM TINT CARD (top project info)          */
/* ============================================ */
.project-show-card {
    background: #ffffff;
    border-radius: 1rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}
.project-show-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
}

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

/* ============================================ */
/* TASK ITEM                                    */
/* ============================================ */
.task-item {
    transition: all 0.3s ease;
    border-bottom: 1px solid #f3f4f6;
    padding: 1rem 0;
}
.task-item:last-child { border-bottom: none; }

/* ============================================ */
/* INLINE PANEL — LIGHT BLUE TINT               */
/* ============================================ */
.inline-panel {
    margin-top: 0.75rem;
    padding: 0.85rem 1rem;
    background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 0.65rem;
    box-shadow: 0 1px 3px rgba(26, 42, 74, 0.04);
}
.inline-panel-title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.form-label-inline {
    display: block;
    font-size: 0.65rem;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}
.form-input-inline {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    border: 1px solid #bfdbfe;
    border-radius: 0.5rem;
    background: #ffffff;
    transition: all 0.15s ease;
}
.form-input-inline:focus {
    border-color: #1a2a4a;
    box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-select-inline {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    border: 1px solid #bfdbfe;
    border-radius: 0.5rem;
    background-color: #ffffff;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 12px;
    padding-right: 2rem;
    transition: all 0.15s ease;
}
.form-select-inline:focus {
    border-color: #1a2a4a;
    box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.08);
    outline: none;
}

/* ============================================ */
/* ACTION BUTTONS                               */
/* ============================================ */
.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.7rem;
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.action-btn.submit  { background: #dbeafe; color: #1e40af; }
.action-btn.submit:hover  { background: #bfdbfe; }
.action-btn.edit    { background: #fef3c7; color: #92400e; }
.action-btn.edit:hover    { background: #fde68a; }
.action-btn.delete  { background: #fee2e2; color: #991b1b; }
.action-btn.delete:hover  { background: #fecaca; }
.action-btn.save    { background: linear-gradient(135deg, #1a2a4a, #2d4a7a); color: #fff; }
.action-btn.save:hover    { box-shadow: 0 4px 12px rgba(26, 42, 74, 0.25); }
.action-btn.cancel  { background: #f3f4f6; color: #475569; }
.action-btn.cancel:hover  { background: #e5e7eb; }

/* ============================================ */
/* SUBMISSIONS SECTION — LIGHT BLUE TINT        */
/* ============================================ */
.submissions-wrap {
    margin-top: 0.85rem;
    padding: 0.85rem 0.95rem;
    background: linear-gradient(135deg, #eff6ff 0%, #f8fbff 100%);
    border: 1px solid #bfdbfe;
    border-radius: 0.75rem;
}
.submissions-wrap-title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

/* ============================================ */
/* SUBMISSION ITEM — WHITE CARD INSIDE TINT     */
/* ============================================ */
.submission-item {
    padding: 0.65rem 0.85rem;
    background: #ffffff;
    border: 1px solid #dbeafe;
    border-radius: 0.55rem;
    margin-bottom: 0.5rem;
    font-size: 0.75rem;
    transition: all 0.15s ease;
}
.submission-item:hover {
    border-color: #93c5fd;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
}
.submission-item:last-child { margin-bottom: 0; }

.submission-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.1rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 700;
}
.sub-pending            { background: #fef3c7; color: #92400e; }
.sub-approved           { background: #d1fae5; color: #065f46; }
.sub-rejected           { background: #fecaca; color: #991b1b; }
.sub-revision_requested { background: #fed7aa; color: #9a3412; }

/* ============================================ */
/* REVIEW FORM — AMBER TINTED BLOCK             */
/* ============================================ */
.review-form {
    margin-top: 0.6rem;
    padding: 0.65rem 0.75rem;
    background: linear-gradient(135deg, #fef9e7 0%, #fff8e1 100%);
    border: 1px solid #fde68a;
    border-radius: 0.55rem;
}
.review-form-label {
    font-size: 0.65rem;
    font-weight: 700;
    color: #92400e;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.5rem;
}

/* ============================================ */
/* MEMBER AVATAR                                */
/* ============================================ */
.member-avatar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem 0.25rem 0.25rem;
    background: #ffffff;
    border-radius: 9999px;
    border: 1px solid #bfdbfe;
}

/* ============================================ */
/* MAIN BUTTONS                                 */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
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
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}
.btn-secondary:hover { background: #e5e7eb; transform: translateY(-1px); }
.btn-danger {
    background: #fef2f2;
    color: #dc2626;
    padding: 0.5rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid #fca5a5;
    cursor: pointer;
}
.btn-danger:hover { background: #fee2e2; border-color: #dc2626; }

/* ============================================ */
/* INFO LIST                                    */
/* ============================================ */
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.6rem 0.9rem;
    background: rgba(255, 255, 255, 0.75);
    border: 1px solid rgba(191, 219, 254, 0.7);
    border-radius: 0.6rem;
    transition: all 0.15s ease;
}
.info-row:hover {
    background: #ffffff;
    border-color: #93c5fd;
}
.info-label {
    font-size: 0.68rem;
    font-weight: 700;
    color: #475569;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.info-value {
    font-size: 0.8rem;
    font-weight: 600;
    color: #1a2a4a;
    text-align: right;
}

@media (max-width: 640px) {
    .project-show-card, .card-tinted { padding: 1rem; }
    .member-avatar { padding: 0.15rem 0.5rem 0.15rem 0.15rem; }
    .member-avatar img { width: 20px; height: 20px; }
}
</style>

@php
    $userId = auth()->id();
    $isCreator = (int) $userId === (int) $project->created_by;
@endphp

<div class="max-w-4xl mx-auto px-4">

    {{-- FLASH --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm space-y-1">
            @foreach($errors->all() as $e)
                <p class="flex items-start gap-2">
                    <i class="fas fa-exclamation-circle mt-0.5 text-xs"></i>
                    <span>{{ $e }}</span>
                </p>
            @endforeach
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- TOP TINTED CARD: header + info + members    --}}
    {{-- ============================================ --}}
    <div class="card-tinted p-6 mb-6">

        {{-- HEADER --}}
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-5">
            <div>
                <h2 class="text-2xl font-bold text-[#1a2a4a] flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm">
                        <i class="fas fa-user-circle"></i>
                    </span>
                    {{ $project->name }}
                </h2>
                @if($project->description)
                    <p class="text-sm text-gray-600 flex items-center gap-1 mt-1">
                        <i class="fas fa-file-alt text-gray-400"></i> {{ $project->description }}
                    </p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($isCreator)
                    <a href="{{ route('personal.invite', $project) }}" class="btn-primary inline-flex items-center gap-1">
                        <i class="fas fa-user-plus"></i> Invite People
                    </a>
                    <button onclick="confirmDeleteProject()" class="btn-danger inline-flex items-center gap-1">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                @endif
                @if(!$isCreator && $project->members()->where('user_id', $userId)->exists())
                    <button onclick="confirmLeaveProject()" class="btn-danger inline-flex items-center gap-1">
                        <i class="fas fa-sign-out-alt"></i> Leave
                    </button>
                @endif
                <a href="{{ route('personal.index') }}" class="btn-secondary inline-flex items-center gap-1">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- PROJECT INFO LIST --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-5">
            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-circle text-green-500" style="font-size:0.5rem;"></i>
                    Status
                </span>
                <span class="info-value
                    @if($project->status === 'active') text-green-700
                    @else text-gray-600 @endif">
                    {{ ucfirst($project->status) }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-tasks text-purple-500"></i>
                    Tasks
                </span>
                <span class="info-value">{{ $project->tasks->count() }}</span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    <i class="fas fa-users text-blue-500"></i>
                    Members
                </span>
                <span class="info-value">{{ $project->members->count() }}</span>
            </div>

            @if($project->deadline)
                <div class="info-row">
                    <span class="info-label">
                        <i class="fas fa-clock text-amber-500"></i>
                        Due Date
                    </span>
                    <span class="info-value">{{ $project->deadline->format('d M Y') }}</span>
                </div>
            @endif

            <div class="info-row {{ $project->deadline ? 'sm:col-span-2' : '' }}">
                <span class="info-label">
                    <i class="fas fa-crown text-indigo-500"></i>
                    Leader
                </span>
                <span class="info-value">{{ $project->creator->name }}</span>
            </div>
        </div>

        {{-- TEAM MEMBERS --}}
        @if($project->members->count() > 0)
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-1">
                    <i class="fas fa-users text-blue-500"></i> Team Members
                </h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($project->members as $member)
                        <div class="member-avatar">
                            <img src="{{ $member->getProfilePictureUrl() }}"
                                 alt="{{ $member->name }}"
                                 class="w-6 h-6 rounded-full object-cover border border-gray-200">
                            <span class="text-sm text-gray-700">{{ $member->name }}</span>
                            @if($member->id === $project->created_by)
                                <span class="text-[0.5rem] text-indigo-500 font-medium">
                                    <i class="fas fa-crown"></i>
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- ============================================ --}}
    {{-- TASKS SECTION — white card                  --}}
    {{-- ============================================ --}}
    <div class="project-show-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-6 h-6 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white text-xs">
                    <i class="fas fa-tasks"></i>
                </span>
                Tasks
            </h3>
            @if($isCreator)
                <button onclick="togglePanel('addTaskForm')" class="btn-primary text-sm inline-flex items-center gap-1">
                    <i class="fas fa-plus-circle"></i> Add Task
                </button>
            @endif
        </div>

        {{-- ADD TASK FORM --}}
        @if($isCreator)
            <div id="addTaskForm" class="hidden mb-4 inline-panel">
                <div class="inline-panel-title">
                    <i class="fas fa-plus-circle"></i> Add New Task
                </div>
                <form method="POST" action="{{ route('tasks.store') }}">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="priority" value="medium">
                    <input type="hidden" name="department_id" value="">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="form-label-inline">Title *</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   placeholder="Task title"
                                   class="form-input-inline">
                        </div>
                        <div>
                            <label class="form-label-inline">Assign To *</label>
                            <select name="assigned_to" required class="form-select-inline">
                                <option value="">Select member...</option>
                                @foreach($project->members as $member)
                                    <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label-inline">Due Date *</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" required
                                   class="form-input-inline">
                        </div>
                        <div>
                            <label class="form-label-inline">Priority</label>
                            <select name="priority_visible" class="form-select-inline" disabled style="opacity:0.6;">
                                <option>Medium (default)</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="action-btn save">
                            <i class="fas fa-check"></i> Create Task
                        </button>
                        <button type="button" onclick="togglePanel('addTaskForm')" class="action-btn cancel">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- TASK LIST --}}
        @forelse($project->tasks as $task)
            @php
                $isAssignee  = (int) $task->assigned_to === (int) $userId;
                $isTaskOwner = (int) $task->created_by === (int) $userId;

                $statusColor = match($task->status) {
                    'done'        => 'green',
                    'in_progress' => 'blue',
                    'review'      => 'purple',
                    'blocked'     => 'red',
                    default       => 'yellow',
                };
                $statusIcon = match($task->status) {
                    'done'        => 'fa-check',
                    'in_progress' => 'fa-spinner',
                    'review'      => 'fa-eye',
                    'blocked'     => 'fa-lock',
                    default       => 'fa-clock',
                };

                $submissions = $task->submissions ?? collect();
                $hasPending = $submissions->where('status', 'pending')->count() > 0;
            @endphp

            <div class="task-item">
                {{-- TASK HEADER ROW --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 flex items-center gap-1">
                            <i class="fas fa-check-square text-[#1a2a4a]"></i>
                            {{ $task->title }}
                        </p>
                        <p class="text-xs text-gray-400 flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-user"></i>
                                {{ $task->assignee?->name ?? 'Unassigned' }}
                            </span>
                            @if($task->due_date)
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-calendar-alt text-amber-400"></i>
                                    {{ $task->due_date->format('d M Y') }}
                                </span>
                            @endif
                            @if($submissions->count() > 0)
                                <span class="inline-flex items-center gap-1 text-indigo-500">
                                    <i class="fas fa-paperclip"></i>
                                    {{ $submissions->count() }} submission(s)
                                </span>
                            @endif
                            @if($hasPending && $isCreator)
                                <span class="inline-flex items-center gap-1 text-amber-600 font-medium">
                                    <i class="fas fa-exclamation-circle"></i>
                                    Needs your review
                                </span>
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[0.6rem] px-2 py-1 rounded-full font-medium whitespace-nowrap inline-flex items-center gap-0.5
                            bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700">
                            <i class="fas {{ $statusIcon }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>

                        @if($isAssignee && $task->status !== 'done')
                            <button type="button" onclick="togglePanel('submit-{{ $task->id }}')" class="action-btn submit">
                                <i class="fas fa-upload"></i> Submit
                            </button>
                        @endif

                        @if($isTaskOwner)
                            <button type="button" onclick="togglePanel('edit-{{ $task->id }}')" class="action-btn edit">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                        @endif

                        @if($isCreator)
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                  onsubmit="return confirm('Delete this task?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- EDIT PANEL --}}
                @if($isTaskOwner)
                    <div id="edit-{{ $task->id }}" class="hidden inline-panel">
                        <div class="inline-panel-title">
                            <i class="fas fa-pen"></i> Edit Task
                        </div>
                        <form method="POST" action="{{ route('tasks.update', $task) }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div class="sm:col-span-2">
                                    <label class="form-label-inline">Title *</label>
                                    <input type="text" name="title" value="{{ $task->title }}" required
                                           class="form-input-inline">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="form-label-inline">Description</label>
                                    <textarea name="description" rows="2"
                                              class="form-input-inline">{{ $task->description }}</textarea>
                                </div>
                                <div>
                                    <label class="form-label-inline">Assign To</label>
                                    <select name="assigned_to" class="form-select-inline">
                                        <option value="">— Unassigned —</option>
                                        @foreach($project->members as $member)
                                            <option value="{{ $member->id }}"
                                                {{ (int) $task->assigned_to === (int) $member->id ? 'selected' : '' }}>
                                                {{ $member->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label-inline">Due Date</label>
                                    <input type="date" name="due_date"
                                           value="{{ $task->due_date?->format('Y-m-d') }}"
                                           class="form-input-inline">
                                </div>
                                <div>
                                    <label class="form-label-inline">Priority</label>
                                    <select name="priority" class="form-select-inline">
                                        <option value="low"    {{ $task->priority === 'low'    ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high"   {{ $task->priority === 'high'   ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label-inline">Status</label>
                                    <select name="status" class="form-select-inline">
                                        @foreach(\App\Models\Task::getStatuses() as $s)
                                            <option value="{{ $s }}" {{ $task->status === $s ? 'selected' : '' }}>
                                                {{ \App\Models\Task::getStatusLabels()[$s] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="action-btn save">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" onclick="togglePanel('edit-{{ $task->id }}')" class="action-btn cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- SUBMIT PANEL --}}
                @if($isAssignee && $task->status !== 'done')
                    <div id="submit-{{ $task->id }}" class="hidden inline-panel">
                        <div class="inline-panel-title">
                            <i class="fas fa-upload"></i> Submit Work
                        </div>
                        <form method="POST" action="{{ route('tasks.submit.upload', $task) }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label-inline">File(s) * (max 10, 20MB each)</label>
                                <input type="file" name="files[]" multiple required
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z,.txt,.csv,.dwg,.dxf"
                                       class="form-input-inline" style="padding: 0.4rem;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-inline">Description (optional)</label>
                                <textarea name="description" rows="2" class="form-input-inline"
                                          placeholder="Brief note about this submission..."></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="action-btn save">
                                    <i class="fas fa-paper-plane"></i> Submit for Review
                                </button>
                                <button type="button" onclick="togglePanel('submit-{{ $task->id }}')" class="action-btn cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- SUBMISSIONS — TINTED WRAPPER --}}
                @if($submissions->count() > 0)
                    <div class="submissions-wrap">
                        <p class="submissions-wrap-title">
                            <i class="fas fa-paperclip text-indigo-500"></i>
                            Submissions ({{ $submissions->count() }})
                        </p>

                        @foreach($submissions->sortByDesc('version') as $sub)
                            @php
                                $isOwnSubmission = (int) $sub->submitted_by === (int) $userId;

                                // Creator can review any pending submission (self-review allowed for personal projects)
                                $canReview = $isCreator && $sub->status === 'pending';

                                $canDelete = $sub->status === 'pending'
                                             && ($isOwnSubmission || $isCreator);
                            @endphp

                            <div class="submission-item">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-gray-900">v{{ $sub->version }}</span>
                                        <span class="submission-status-pill sub-{{ $sub->status }}">
                                            @if($sub->status === 'approved') <i class="fas fa-check"></i>
                                            @elseif($sub->status === 'rejected') <i class="fas fa-times"></i>
                                            @elseif($sub->status === 'revision_requested') <i class="fas fa-undo"></i>
                                            @else <i class="fas fa-clock"></i> @endif
                                            {{ ucfirst(str_replace('_', ' ', $sub->status)) }}
                                        </span>
                                        <span class="text-gray-600 truncate max-w-[180px]">
                                            <i class="fas fa-file text-purple-400"></i>
                                            {{ $sub->file_name }}
                                        </span>
                                        @if($sub->submitted_at)
                                            <span class="text-gray-400 text-[0.65rem]">
                                                {{ $sub->submitted_at->format('d M Y H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('tasks.submissions.download', $sub) }}"
                                           class="action-btn edit" title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        @if($canDelete)
                                            <form method="POST" action="{{ route('tasks.submissions.delete', $sub) }}"
                                                  onsubmit="return confirm('Delete this submission?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                @if($sub->description)
                                    <p class="text-gray-600 mt-1">{{ $sub->description }}</p>
                                @endif

                                @if($sub->review_notes)
                                    <div class="mt-1.5 p-1.5 bg-blue-50 rounded border border-blue-100 text-blue-700 text-[0.7rem]">
                                        <strong><i class="fas fa-comment"></i> Review:</strong> {{ $sub->review_notes }}
                                        @if($sub->reviewer)
                                            <span class="text-blue-500 ml-1">— {{ $sub->reviewer->name }}</span>
                                        @endif
                                    </div>
                                @endif

                                {{-- REVIEW FORM — tinted, clearly labeled --}}
                                @if($canReview)
                                    <div class="review-form">
                                        <div class="review-form-label">
                                            <i class="fas fa-gavel"></i> Review this submission
                                        </div>
                                        <form method="POST" action="{{ route('tasks.submissions.review', $sub) }}"
                                              class="flex flex-col sm:flex-row gap-2">
                                            @csrf
                                            <select name="status" class="form-select-inline flex-shrink-0" style="width:auto;">
                                                <option value="approved">✅ Approve</option>
                                                <option value="revision_requested">📝 Request Revision</option>
                                                <option value="rejected">❌ Reject</option>
                                            </select>
                                            <input type="text" name="review_notes" placeholder="Review notes..."
                                                   class="form-input-inline flex-1">
                                            <button type="submit" class="action-btn save">
                                                <i class="fas fa-check-circle"></i> Submit Review
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-6">
                <div class="text-3xl mb-2 text-gray-300">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="text-gray-400 text-sm">No tasks yet.</p>
                @if($isCreator)
                    <p class="text-xs text-gray-400 mt-1">Click <strong>Add Task</strong> to get started.</p>
                @endif
            </div>
        @endforelse
    </div>

    {{-- YOUR TASKS (Non-creators) --}}
    @if(!$isCreator)
        <div class="mt-6 project-show-card p-6">
            <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <span class="w-6 h-6 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white text-xs">
                    <i class="fas fa-tasks"></i>
                </span>
                Your Tasks
            </h3>
            @php
                $myTasks = $project->tasks->where('assigned_to', $userId);
            @endphp
            @forelse($myTasks as $task)
                <div class="task-item flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <p class="text-sm font-medium text-gray-800 flex items-center gap-1">
                            <i class="fas fa-check-square text-[#1a2a4a]"></i>
                            {{ $task->title }}
                        </p>
                        <p class="text-xs text-gray-400">
                            Status: {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="togglePanel('submit-{{ $task->id }}')" class="action-btn submit">
                            <i class="fas fa-upload"></i> Submit
                        </button>
                        <span class="text-[0.6rem] px-2 py-1 rounded-full font-medium
                            @if($task->status === 'done') bg-green-100 text-green-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>

                @if((int) $task->assigned_to === (int) $userId && $task->status !== 'done')
                    <div id="submit-{{ $task->id }}" class="hidden inline-panel">
                        <div class="inline-panel-title"><i class="fas fa-upload"></i> Submit Work</div>
                        <form method="POST" action="{{ route('tasks.submit.upload', $task) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label-inline">File(s) *</label>
                                <input type="file" name="files[]" multiple required
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z,.txt,.csv,.dwg,.dxf"
                                       class="form-input-inline" style="padding: 0.4rem;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label-inline">Description (optional)</label>
                                <textarea name="description" rows="2" class="form-input-inline"></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="action-btn save">
                                    <i class="fas fa-paper-plane"></i> Submit for Review
                                </button>
                                <button type="button" onclick="togglePanel('submit-{{ $task->id }}')" class="action-btn cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @empty
                <div class="text-center py-4">
                    <p class="text-gray-400 text-sm">No tasks assigned to you.</p>
                </div>
            @endforelse
        </div>
    @endif
</div>

{{-- HIDDEN FORMS --}}
@if($isCreator)
    <form id="deleteProjectForm" method="POST" action="{{ route('personal.destroy', $project) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endif
@if(!$isCreator && $project->members()->where('user_id', $userId)->exists())
    <form id="leaveProjectForm" method="POST" action="{{ route('personal.leave', $project) }}" style="display: none;">
        @csrf
    </form>
@endif

<script>
function togglePanel(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('hidden');
}

function confirmDeleteProject() {
    if (confirm('Delete this personal project?\n\nAll tasks, files, and member access will be removed.\nThis cannot be undone!')) {
        document.getElementById('deleteProjectForm').submit();
    }
}

function confirmLeaveProject() {
    if (confirm('Leave this project?\n\nYou will lose access to all tasks and data.')) {
        document.getElementById('leaveProjectForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        const addTaskForm = document.getElementById('addTaskForm');
        if (addTaskForm) addTaskForm.classList.remove('hidden');
    @endif
});
</script>

@endsection