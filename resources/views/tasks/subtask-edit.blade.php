{{-- resources/views/tasks/subtask-edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Sub-task')

@section('content')

<style>
/* ============================================ */
/* BASE FORM CONTROLS                            */
/* ============================================ */
.form-input, .form-select, .form-textarea {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover, .form-select:hover, .form-textarea:hover {
    border-color: #cbd5e1;
}
.form-input:focus, .form-select:focus, .form-textarea:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder, .form-textarea::placeholder {
    color: #9ca3af;
}
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    background-size: 12px;
    padding-right: 2.2rem;
}
.form-input:disabled, .form-select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* ============================================ */
/* LABELS                                        */
/* ============================================ */
.form-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 0.45rem;
}

/* ============================================ */
/* SECTION CARDS                                 */
/* ============================================ */
.form-section {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.2s ease;
}
.form-section:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.9rem;
}
.section-title .icon {
    width: 24px;
    height: 24px;
    border-radius: 7px;
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.68rem;
    flex-shrink: 0;
}
.section-title .section-hint {
    margin-left: auto;
    font-size: 0.68rem;
    font-weight: 400;
    color: #94a3b8;
    letter-spacing: 0;
    text-transform: none;
}

/* ============================================ */
/* SKILL PICKER                                  */
/* ============================================ */
.skill-picker {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    padding: 0.85rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.65rem;
    background: #fafbfc;
    max-height: 180px;
    overflow-y: auto;
}
.skill-picker::-webkit-scrollbar { width: 5px; }
.skill-picker::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

.skill-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.8rem;
    border-radius: 9999px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #64748b;
    font-size: 0.72rem;
    cursor: pointer;
    user-select: none;
    transition: all 0.15s ease;
    font-weight: 500;
}
.skill-chip:hover {
    border-color: #94a3b8;
    color: #1a2a4a;
    transform: translateY(-1px);
}
.skill-chip.selected {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    border-color: #1a2a4a;
    color: #fff;
    box-shadow: 0 3px 10px rgba(26, 42, 74, 0.22);
    transform: translateY(-1px);
}

/* ============================================ */
/* MEMBER OPTION                                 */
/* ============================================ */
.member-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.15s ease;
    margin-bottom: 0.5rem;
    position: relative;
    background: #fff;
}
.member-option:last-child { margin-bottom: 0; }
.member-option:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.member-option.selected {
    border-color: #1a2a4a;
    background: linear-gradient(135deg, #f8fafc, #eff6ff);
    box-shadow: 0 4px 14px rgba(26, 42, 74, 0.08);
}
.member-option.selected::before {
    content: '';
    position: absolute;
    left: 0; top: 50%;
    transform: translateY(-50%);
    width: 3px; height: 60%;
    background: linear-gradient(180deg, #818cf8, #a78bfa);
    border-radius: 0 4px 4px 0;
}
.member-option.recommended {
    border-color: #fbbf24;
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
}
.member-option.recommended::after {
    content: '⚡ AI Pick';
    position: absolute;
    top: -8px; right: 12px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; font-size: 0.55rem; font-weight: 700;
    padding: 0.15rem 0.5rem; border-radius: 9999px;
    letter-spacing: 0.03em;
}

.workload-track { width: 100%; height: 5px; background: #f1f5f9; border-radius: 9999px; overflow: hidden; margin-top: 0.4rem; }
.workload-fill { height: 100%; border-radius: 9999px; transition: width 0.5s ease; }
.workload-green  { background: linear-gradient(90deg, #34d399, #059669); }
.workload-yellow { background: linear-gradient(90deg, #fbbf24, #d97706); }
.workload-orange { background: linear-gradient(90deg, #fb923c, #ea580c); }
.workload-red    { background: linear-gradient(90deg, #f87171, #dc2626); }
.workload-percent { font-size: 0.68rem; font-weight: 700; }
.workload-percent.green  { color: #059669; }
.workload-percent.yellow { color: #d97706; }
.workload-percent.orange { color: #ea580c; }
.workload-percent.red    { color: #dc2626; }

.skill-tag { display: inline-block; padding: 0.12rem 0.5rem; border-radius: 9999px; font-size: 0.6rem; font-weight: 500; margin-right: 0.2rem; }
.skill-tag.matched { background: #d1fae5; color: #065f46; }
.skill-tag.missing { background: #f1f5f9; color: #94a3b8; }

/* ============================================ */
/* DEPENDENCY ITEM                               */
/* ============================================ */
.dep-item {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.6rem 0.85rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.6rem;
    margin-bottom: 0.35rem;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 0.8rem;
    background: #fafbfc;
}
.dep-item:last-child { margin-bottom: 0; }
.dep-item:hover { border-color: #94a3b8; background: #f8fafc; }
.dep-item:has(input:checked) {
    border-color: #1a2a4a;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
}
.dep-item input { accent-color: #1a2a4a; width: 16px; height: 16px; }

/* ============================================ */
/* READ-ONLY ASSIGNEE (non-leader view)          */
/* ============================================ */
.readonly-assignee {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 0.65rem;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* ============================================ */
/* HOVER POPUP                                   */
/* ============================================ */
.member-popup {
    position: fixed; z-index: 9999; pointer-events: none; opacity: 0;
    transform: translateY(6px) scale(0.98);
    transition: opacity 0.18s ease, transform 0.18s ease;
    width: 380px;
}
.member-popup.visible {
    opacity: 1; transform: translateY(0) scale(1); pointer-events: auto;
}
.member-popup-inner {
    background: #fff; border-radius: 0.875rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16), 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}
.member-popup-header {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff; position: relative;
}
.member-popup-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    object-fit: cover; border: 2px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
}
.member-popup-name { font-size: 0.875rem; font-weight: 600; color: #fff; margin: 0; line-height: 1.2; }
.member-popup-email {
    font-size: 0.7rem; color: rgba(255,255,255,0.75);
    margin: 0.15rem 0 0 0; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
}
.member-popup-status {
    position: absolute; top: 0.5rem; right: 0.5rem;
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.15rem 0.5rem; border-radius: 9999px;
    font-size: 0.55rem; font-weight: 600;
    background: rgba(255,255,255,0.2); color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(4px);
}
.member-popup-body { padding: 0.75rem 1rem; }
.member-popup-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 0.5rem; padding: 0.4rem 0;
    border-bottom: 1px solid #f3f4f6; font-size: 0.75rem;
}
.member-popup-row:last-child { border-bottom: none; }
.member-popup-label {
    color: #6b7280; font-weight: 500;
    display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;
}
.member-popup-label i { color: #9ca3af; font-size: 0.7rem; }
.member-popup-value {
    color: #111827; font-weight: 500;
    text-align: right; word-break: break-word;
}
.member-popup-tags {
    display: flex; flex-wrap: wrap; gap: 0.25rem; justify-content: flex-end;
}
.member-popup-tag {
    display: inline-block; padding: 0.1rem 0.45rem;
    border-radius: 9999px; font-size: 0.6rem; font-weight: 500;
    background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe;
}
.member-popup-tag.matched { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
.member-popup-tag.missing { background: #f1f5f9; color: #94a3b8; border-color: #e5e7eb; }
.member-popup-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    margin: 0.5rem 0;
}
.member-popup-workload-title {
    font-size: 0.7rem; font-weight: 600; color: #6b7280;
    display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.5rem;
}
.member-popup-workload-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;
}
.member-popup-workload-item {
    text-align: center; padding: 0.4rem 0.25rem;
    background: #f8fafc; border-radius: 0.5rem; border: 1px solid #f1f5f9;
}
.member-popup-workload-item .w-total,
.member-popup-workload-item .w-pending,
.member-popup-workload-item .w-completed {
    display: block; font-size: 0.95rem; font-weight: 700; line-height: 1;
}
.member-popup-workload-item .w-total { color: #1a2a4a; }
.member-popup-workload-item .w-pending { color: #d97706; }
.member-popup-workload-item .w-completed { color: #059669; }
.member-popup-workload-item .w-label {
    display: block; font-size: 0.55rem; color: #9ca3af;
    text-transform: uppercase; letter-spacing: 0.03em; margin-top: 0.2rem;
}

.member-popup-breakdown {
    width: 100%; margin-top: 0.35rem;
    display: flex; flex-direction: column; gap: 0.35rem;
}
.breakdown-item {
    display: flex; align-items: center; gap: 0.5rem; font-size: 0.68rem;
}
.breakdown-item .bd-label {
    flex: 1; color: #6b7280;
    display: flex; align-items: center; gap: 0.3rem; min-width: 0;
}
.breakdown-item .bd-label span {
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.breakdown-item .bd-bar {
    width: 80px; height: 5px; background: #f1f5f9;
    border-radius: 9999px; overflow: hidden; flex-shrink: 0;
}
.breakdown-item .bd-bar-fill {
    height: 100%; border-radius: 9999px;
    background: linear-gradient(90deg, #93c5fd, #3b82f6);
    transition: width 0.4s ease;
}
.breakdown-item .bd-value {
    width: 60px; text-align: right; font-weight: 600;
    color: #1f2937; flex-shrink: 0;
}

/* ============================================ */
/* BUTTONS                                       */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.75rem 1.75rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.18s ease;
    box-shadow: 0 2px 8px rgba(26, 42, 74, 0.15);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
}
.btn-primary:active { transform: translateY(0); }

.btn-secondary {
    background: #f3f4f6;
    color: #475569;
    padding: 0.75rem 1.5rem;
    border-radius: 0.7rem;
    font-weight: 600;
    font-size: 0.875rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.4rem;
    transition: all 0.18s ease;
    border: none;
    cursor: pointer;
}
.btn-secondary:hover {
    background: #e5e7eb;
    color: #1f2937;
    transform: translateY(-1px);
}

/* ============================================ */
/* ACTION BAR                                    */
/* ============================================ */
.action-bar {
    position: sticky;
    bottom: 0;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-top: 1px solid #e5e7eb;
    border-radius: 1rem 1rem 0 0;
    padding: 1rem 1.5rem;
    margin-top: 1rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    z-index: 10;
}
</style>

@php
$skills = [
    'Road Design', 'Highway Engineering', 'Drainage System', 'Earthworks',
    'Slope Design', 'Site Grading', 'Water Reticulation', 'Sewerage System',
    'Structural Design', 'Steel Structure', 'Reinforced Concrete',
    'Foundation Design', 'Piling', 'Pre-stressed Concrete',
    'AutoCAD', 'Civil 3D', 'Revit', 'MicroStation', 'BIM Modeling',
    'ETABS', 'STAAD Pro', 'SAP2000', 'SketchUp',
    'Project Management', 'Site Supervision', 'Quality Control',
    'Scheduling', 'Cost Estimation', 'Contract Administration',
    'Documentation', 'Submissions', 'OSC', 'BOMBA', 'JPS', 'IWK',
];

$currentSkills = old('skills_required', $task->skills_required ?? []);

$user = auth()->user();
$isLeader = $parent && $parent->assigned_to === $user->id;
$canReassign = $isLeader;
@endphp

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-6">
        <span class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-lg shadow-md">
            <i class="fas fa-pen"></i>
        </span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Edit Sub-task</h2>
            <p class="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                <i class="fas fa-clipboard-list text-blue-400 text-xs"></i>
                Under: {{ $parent->title }}
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.subtask.update', $task) }}" id="editSubtaskForm">
        @csrf
        @method('PUT')

        {{-- SECTION 1: Sub-task Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                Sub-task Details
            </div>

            <div class="mb-4">
                <label class="form-label">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                       class="form-input">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3"
                          class="form-textarea">{{ old('description', $task->description) }}</textarea>
            </div>
        </div>

        {{-- SECTION 2: Skills --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-tags"></i></span>
                Skills Required
                <span class="section-hint">Used by AI to match the best assignee</span>
            </div>
            <div class="skill-picker" id="skillPicker">
                @foreach($skills as $skill)
                    <span class="skill-chip {{ in_array($skill, $currentSkills) ? 'selected' : '' }}"
                          data-skill="{{ $skill }}">{{ $skill }}</span>
                @endforeach
            </div>
            <div id="skillsHiddenInputs"></div>
        </div>

        {{-- SECTION 3: Department & Schedule --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                Department & Schedule
                @if(!$canReassign)
                    <span class="section-hint">
                        <i class="fas fa-lock mr-1"></i> Only the leader can change these
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <div class="lg:col-span-2">
                    <label class="form-label">Department</label>
                    <select name="department_id" id="departmentSelect"
                            {{ $canReassign ? '' : 'disabled' }}
                            class="form-select">
                        <option value="">— Standalone —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department_id', $task->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date"
                                   value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}"
                                   {{ $canReassign ? '' : 'disabled' }}
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date"
                                   value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                                   {{ $canReassign ? '' : 'disabled' }}
                                   class="form-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: Assigned Member --}}
        @if($canReassign)
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-user-check"></i></span>
                    Assigned Member
                </div>
                <div id="membersContainer">
                    <div class="text-center py-10 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-dashed border-gray-200">
                        <p class="text-sm text-gray-400">Loading members...</p>
                    </div>
                </div>
                <input type="hidden" name="assigned_to" id="assignedToInput"
                       value="{{ old('assigned_to', $task->assigned_to) }}">
                @error('assigned_to') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @else
            {{-- Read-only assignee for non-leader --}}
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-user-check"></i></span>
                    Assigned Member
                    <span class="section-hint">
                        <i class="fas fa-lock mr-1"></i> Leader-only reassignment
                    </span>
                </div>
                @if($task->assignee)
                    <div class="readonly-assignee">
                        <img src="{{ $task->assignee->getProfilePictureUrl() }}"
                             class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $task->assignee->name }}</p>
                            <p class="text-xs text-gray-500">{{ $task->assignee->email }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Unassigned</p>
                @endif
            </div>
        @endif

        {{-- SECTION 5: Dependencies (conditional) --}}
        @if($canReassign && $siblings->count() > 0)
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-link"></i></span>
                    Dependencies
                    <span class="section-hint">Blocks this sub-task until selected are done</span>
                </div>
                <div class="max-h-48 overflow-y-auto">
                    @foreach($siblings as $sib)
                        <label class="dep-item">
                            <input type="checkbox" name="dependencies[]" value="{{ $sib->id }}"
                                   {{ in_array($sib->id, old('dependencies', $currentDependencies)) ? 'checked' : '' }}>
                            <span class="flex-1">{{ $sib->title }}</span>
                            <span class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', $sib->status) }}</span>
                            @if($sib->due_date)
                                <span class="text-xs text-gray-400">Due {{ $sib->due_date->format('d M') }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="sm:w-44">
                <label class="form-label mb-1">Status</label>
                <select name="status" class="form-select text-sm">
                    @foreach(\App\Models\Task::getStatuses() as $s)
                        <option value="{{ $s }}" {{ old('status', $task->status) === $s ? 'selected' : '' }}>
                            {{ \App\Models\Task::getStatusLabels()[$s] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:w-36">
                <label class="form-label mb-1">Priority</label>
                <select name="priority" class="form-select text-sm">
                    <option value="low"    {{ old('priority', $task->priority) === 'low'    ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"   {{ old('priority', $task->priority) === 'high'   ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div class="flex-1"></div>
            <a href="{{ route('tasks.subtask.show', $task) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

{{-- HOVER POPUP --}}
<div id="memberPopup" class="member-popup">
    <div class="member-popup-inner">
        <div class="member-popup-header">
            <img id="popupAvatar" src="" alt="" class="member-popup-avatar">
            <div class="min-w-0 flex-1">
                <p id="popupName" class="member-popup-name"></p>
                <p id="popupEmail" class="member-popup-email"></p>
            </div>
            <span id="popupStatusBadge" class="member-popup-status"></span>
        </div>

        <div class="member-popup-body">
            <div class="member-popup-row">
                <span class="member-popup-label"><i class="fas fa-briefcase"></i> Job Scope</span>
                <span id="popupJobScope" class="member-popup-value"></span>
            </div>
            <div class="member-popup-row">
                <span class="member-popup-label"><i class="fas fa-trophy"></i> AI Score</span>
                <span id="popupScore" class="member-popup-value"></span>
            </div>

            <div class="member-popup-row">
                <span class="member-popup-label"><i class="fas fa-check-circle"></i> Matches</span>
                <div id="popupMatched" class="member-popup-tags"></div>
            </div>
            <div class="member-popup-row">
                <span class="member-popup-label"><i class="fas fa-times-circle"></i> Missing</span>
                <div id="popupMissing" class="member-popup-tags"></div>
            </div>

            <div class="member-popup-row" id="popupBreakdownRow" style="display:none;">
                <div style="width:100%;">
                    <span class="member-popup-label" style="margin-bottom:0.35rem;">
                        <i class="fas fa-calculator"></i> Score Breakdown
                    </span>
                    <div class="member-popup-breakdown" id="popupBreakdown"></div>
                </div>
            </div>

            <div class="member-popup-divider"></div>
            <div class="member-popup-workload-title">
                <i class="fas fa-chart-bar"></i> Workload
            </div>
            <div class="member-popup-workload-grid">
                <div class="member-popup-workload-item">
                    <span class="w-total" id="popupTotal">0</span>
                    <span class="w-label">Total</span>
                </div>
                <div class="member-popup-workload-item">
                    <span class="w-pending" id="popupPending">0</span>
                    <span class="w-label">Pending</span>
                </div>
                <div class="member-popup-workload-item">
                    <span class="w-completed" id="popupCompleted">0</span>
                    <span class="w-label">Done</span>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-500 font-medium">Capacity used</span>
                    <span id="popupWorkloadPct" class="workload-percent">0%</span>
                </div>
                <div class="workload-track">
                    <div id="popupWorkloadFill" class="workload-fill workload-green" style="width:0%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($canReassign)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form          = document.getElementById('editSubtaskForm');
    const deptSelect    = document.getElementById('departmentSelect');
    const membersBox    = document.getElementById('membersContainer');
    const assignedInput = document.getElementById('assignedToInput');
    const skillPicker   = document.getElementById('skillPicker');
    const hiddenBox     = document.getElementById('skillsHiddenInputs');

    const popup           = document.getElementById('memberPopup');
    const popupAvatar     = document.getElementById('popupAvatar');
    const popupName       = document.getElementById('popupName');
    const popupEmail      = document.getElementById('popupEmail');
    const popupStatusBadge= document.getElementById('popupStatusBadge');
    const popupJobScope   = document.getElementById('popupJobScope');
    const popupScore      = document.getElementById('popupScore');
    const popupMatched    = document.getElementById('popupMatched');
    const popupMissing    = document.getElementById('popupMissing');
    const popupTotal      = document.getElementById('popupTotal');
    const popupPending    = document.getElementById('popupPending');
    const popupCompleted  = document.getElementById('popupCompleted');
    const popupWorkloadPct= document.getElementById('popupWorkloadPct');
    const popupWorkloadFill = document.getElementById('popupWorkloadFill');
    const popupBreakdown  = document.getElementById('popupBreakdown');
    const popupBreakdownRow = document.getElementById('popupBreakdownRow');

    const selectedSkills = new Set(
        Array.from(skillPicker.querySelectorAll('.skill-chip.selected'))
             .map(el => el.dataset.skill)
    );

    let hoverTimeout = null;

    function refreshMembers() {
        if (deptSelect.value) deptSelect.dispatchEvent(new Event('change'));
    }

    skillPicker.querySelectorAll('.skill-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            const skill = this.dataset.skill;
            if (selectedSkills.has(skill)) {
                selectedSkills.delete(skill);
                this.classList.remove('selected');
            } else {
                selectedSkills.add(skill);
                this.classList.add('selected');
            }
            refreshMembers();
        });
    });

    form.addEventListener('submit', function () {
        hiddenBox.innerHTML = '';
        selectedSkills.forEach(function (skill) {
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'skills_required[]';
            h.value = skill;
            hiddenBox.appendChild(h);
        });
    });

    function setEmpty() {
        membersBox.innerHTML = `
            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-sm text-gray-400">Pick a department to see available members</p>
            </div>`;
    }

    deptSelect.addEventListener('change', function () {
        const deptId = this.value;
        if (!deptId) { setEmpty(); return; }

        membersBox.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
            </div>`;

        const skills = Array.from(selectedSkills);
        const qs = skills.map(s => 'skills[]=' + encodeURIComponent(s)).join('&');
        const url = `/tasks/departments/${deptId}/members-with-workload` + (qs ? '?' + qs : '');

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.members || data.members.length === 0) {
                    membersBox.innerHTML = `
                        <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                            <p class="text-sm text-gray-400">No members in this department</p>
                        </div>`;
                    return;
                }
                renderMembers(data.members);
            })
            .catch(() => {
                membersBox.innerHTML = `<p class="text-sm text-red-500">Failed to load members.</p>`;
            });
    });

    function renderMembers(members) {
        membersBox.innerHTML = '';
        members.forEach(function (m) {
            const isCurrent = String(m.id) === String(assignedInput.value);
            const rec = m.is_recommended && !isCurrent ? 'recommended' : '';
            const sel = isCurrent ? 'selected' : '';
            const wc  = m.workload_color;
            const pct = m.workload_percent;
            let skillTags = '';
            (m.matched_skills || []).forEach(s => skillTags += `<span class="skill-tag matched">${s}</span>`);
            (m.missing_skills || []).forEach(s => skillTags += `<span class="skill-tag missing">${s}</span>`);

            const opt = document.createElement('div');
            opt.className = `member-option ${rec} ${sel}`;
            opt.dataset.userId = m.id;
            opt.dataset.member = JSON.stringify(m);
            opt.innerHTML = `
                <img src="${m.profile_picture}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-medium text-gray-900 truncate">${m.name}</p>
                        <span class="text-[0.6rem] text-gray-400">${m.job_scope ?? ''}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-0.5 text-[0.65rem] text-gray-400">
                        <span>${m.pending_tasks} pending · ${m.completed_tasks} done</span>
                        <span class="ml-auto workload-percent ${wc}">${pct}%</span>
                    </div>
                    <div class="workload-track">
                        <div class="workload-fill workload-${wc}" style="width:${pct}%"></div>
                    </div>
                    ${skillTags ? `<div class="mt-1">${skillTags}</div>` : ''}
                </div>
            `;
            membersBox.appendChild(opt);
        });

        membersBox.querySelectorAll('.member-option').forEach(el => {
            el.addEventListener('click', function () {
                membersBox.querySelectorAll('.member-option').forEach(x => x.classList.remove('selected'));
                this.classList.add('selected');
                assignedInput.value = this.dataset.userId;
            });

            el.addEventListener('mouseenter', function () {
                clearTimeout(hoverTimeout);
                hoverTimeout = setTimeout(() => {
                    try { showPopup(this, JSON.parse(this.dataset.member)); }
                    catch (e) { console.error('Popup parse error:', e); }
                }, 120);
            });
            el.addEventListener('mouseleave', function () {
                clearTimeout(hoverTimeout);
                hidePopup();
            });
        });
    }

    function positionPopup(anchor) {
        const rect = anchor.getBoundingClientRect();
        const popupWidth = 380;
        const popupHeight = popup.offsetHeight || 420;
        const margin = 12;
        const vw = window.innerWidth;
        const vh = window.innerHeight;

        let left = rect.left - popupWidth - margin;
        if (left < margin) left = rect.right + margin;
        if (left + popupWidth > vw - margin) left = vw - popupWidth - margin;

        let top = rect.top;
        if (top + popupHeight > vh - margin) top = vh - popupHeight - margin;
        if (top < margin) top = margin;

        popup.style.left = left + 'px';
        popup.style.top = top + 'px';
    }

    function showPopup(anchor, m) {
        popupAvatar.src = m.profile_picture || '';
        popupName.textContent = m.name || '';
        popupEmail.textContent = m.email || '';
        popupStatusBadge.innerHTML = `<i class="fas ${m.status_icon || 'fa-circle'}"></i> ${m.status_label || ''}`;
        popupJobScope.textContent = m.job_scope || '—';
        popupScore.textContent = (m.ai_score ?? m.score ?? 0) + '%';

        popupMatched.innerHTML = '';
        (m.matched_skills || []).forEach(s => {
            const t = document.createElement('span');
            t.className = 'member-popup-tag matched';
            t.textContent = s;
            popupMatched.appendChild(t);
        });
        if (!m.matched_skills || !m.matched_skills.length) {
            popupMatched.innerHTML = '<span class="text-xs text-gray-400">None</span>';
        }

        popupMissing.innerHTML = '';
        (m.missing_skills || []).forEach(s => {
            const t = document.createElement('span');
            t.className = 'member-popup-tag missing';
            t.textContent = s;
            popupMissing.appendChild(t);
        });
        if (!m.missing_skills || !m.missing_skills.length) {
            popupMissing.innerHTML = '<span class="text-xs text-gray-400">None 🎉</span>';
        }

        popupBreakdown.innerHTML = '';

        const weights = m.ai_weighting || {
            skill_match:        0.35,
            experience_match:   0.20,
            workload_score:     0.20,
            performance_score:  0.15,
            availability_score: 0.10,
        };

        const labels = {
            skill_match:        { text: 'Skills match',  icon: 'fa-tags' },
            experience_match:   { text: 'Experience',    icon: 'fa-chart-line' },
            workload_score:     { text: 'Workload',      icon: 'fa-balance-scale' },
            performance_score:  { text: 'Performance',   icon: 'fa-trophy' },
            availability_score: { text: 'Availability',  icon: 'fa-user-check' },
        };

        const breakdown = m.ai_breakdown || null;

        if (breakdown && Object.keys(breakdown).length) {
            popupBreakdownRow.style.display = '';
            let totalContribution = 0;

            Object.keys(labels).forEach(key => {
                if (!(key in breakdown)) return;

                const raw = Number(breakdown[key]) || 0;
                const weight = weights[key] ?? 0;
                const contribution = raw * weight;
                totalContribution += contribution;

                const pct = Math.min(contribution, 100);

                const item = document.createElement('div');
                item.className = 'breakdown-item';
                item.innerHTML = `
                    <span class="bd-label">
                        <i class="fas ${labels[key].icon} text-gray-400"></i>
                        <span>${labels[key].text}</span>
                    </span>
                    <div class="bd-bar">
                        <div class="bd-bar-fill" style="width: ${pct}%"></div>
                    </div>
                    <span class="bd-value">${contribution.toFixed(1)}%</span>
                `;
                popupBreakdown.appendChild(item);
            });

            const totalEl = document.createElement('div');
            totalEl.className = 'breakdown-item mt-1 pt-2 border-t border-gray-100';
            totalEl.innerHTML = `
                <span class="bd-label font-semibold text-gray-700">
                    <i class="fas fa-calculator text-gray-400"></i>
                    <span>Total</span>
                </span>
                <span class="bd-value font-bold" style="color:#1a2a4a;">${totalContribution.toFixed(1)}%</span>
            `;
            popupBreakdown.appendChild(totalEl);
        } else {
            popupBreakdownRow.style.display = 'none';
        }

        popupTotal.textContent     = m.total_tasks ?? ((m.pending_tasks ?? 0) + (m.completed_tasks ?? 0));
        popupPending.textContent   = m.pending_tasks ?? 0;
        popupCompleted.textContent = m.completed_tasks ?? 0;

        const pct = m.workload_percent ?? 0;
        const wc  = m.workload_color ?? 'green';
        popupWorkloadPct.textContent = pct + '%';
        popupWorkloadPct.className = `workload-percent ${wc}`;
        popupWorkloadFill.className = `workload-fill workload-${wc}`;
        popupWorkloadFill.style.width = pct + '%';

        positionPopup(anchor);
        requestAnimationFrame(() => popup.classList.add('visible'));
    }

    function hidePopup() {
        popup.classList.remove('visible');
    }

    window.addEventListener('scroll', hidePopup, true);
    window.addEventListener('resize', hidePopup);

    if (deptSelect.value) {
        deptSelect.dispatchEvent(new Event('change'));
    } else {
        membersBox.innerHTML = `
            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-sm text-gray-400">Standalone — no department selected</p>
            </div>`;
    }
});
</script>
@endif

@endsection