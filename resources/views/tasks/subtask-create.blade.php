{{-- resources/views/tasks/subtask-create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add Sub-task')

@section('content')

<style>
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
.form-input::placeholder, .form-textarea::placeholder { color: #9ca3af; }
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    background-size: 12px;
    padding-right: 2.2rem;
}

.form-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 0.45rem;
}

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
}

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

.leader-option {
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
.leader-option:last-child { margin-bottom: 0; }
.leader-option:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}
.leader-option.selected {
    border-color: #1a2a4a;
    background: linear-gradient(135deg, #f8fafc, #eff6ff);
    box-shadow: 0 4px 14px rgba(26, 42, 74, 0.08);
}
.leader-option.selected::before {
    content: '';
    position: absolute;
    left: 0; top: 50%;
    transform: translateY(-50%);
    width: 3px; height: 60%;
    background: linear-gradient(180deg, #818cf8, #a78bfa);
    border-radius: 0 4px 4px 0;
}
.leader-option.recommended {
    border-color: #fbbf24;
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
}
.leader-option.recommended::after {
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

/* Hover popup */
.leader-popup {
    position: fixed; z-index: 9999; pointer-events: none;
    opacity: 0; transform: translateY(6px) scale(0.98);
    transition: opacity 0.18s ease, transform 0.18s ease;
    width: 380px;
}
.leader-popup.visible {
    opacity: 1; transform: translateY(0) scale(1); pointer-events: auto;
}
.leader-popup-inner {
    background: #fff; border-radius: 0.875rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16), 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}
.leader-popup-header {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.875rem 1rem;
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff; position: relative;
}
.leader-popup-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    object-fit: cover; border: 2px solid rgba(255,255,255,0.3);
    flex-shrink: 0;
}
.leader-popup-name { font-size: 0.875rem; font-weight: 600; color: #fff; margin: 0; line-height: 1.2; }
.leader-popup-email {
    font-size: 0.7rem; color: rgba(255,255,255,0.75);
    margin: 0.15rem 0 0 0; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap;
}
.leader-popup-status {
    position: absolute; top: 0.5rem; right: 0.5rem;
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.15rem 0.5rem; border-radius: 9999px;
    font-size: 0.55rem; font-weight: 600;
    background: rgba(255,255,255,0.2); color: #fff;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(4px);
}
.leader-popup-body { padding: 0.75rem 1rem; }
.leader-popup-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 0.5rem; padding: 0.4rem 0;
    border-bottom: 1px solid #f3f4f6; font-size: 0.75rem;
}
.leader-popup-row:last-child { border-bottom: none; }
.leader-popup-label {
    color: #6b7280; font-weight: 500;
    display: flex; align-items: center; gap: 0.35rem; flex-shrink: 0;
}
.leader-popup-label i { color: #9ca3af; font-size: 0.7rem; }
.leader-popup-value {
    color: #111827; font-weight: 500;
    text-align: right; word-break: break-word;
}
.leader-popup-tags {
    display: flex; flex-wrap: wrap; gap: 0.25rem; justify-content: flex-end;
}
.leader-popup-tag {
    display: inline-block; padding: 0.1rem 0.45rem;
    border-radius: 9999px; font-size: 0.6rem; font-weight: 500;
    background: #eff6ff; color: #1e40af; border: 1px solid #dbeafe;
}
.leader-popup-tag.matched { background: #d1fae5; color: #065f46; border-color: #a7f3d0; }
.leader-popup-tag.missing { background: #f1f5f9; color: #94a3b8; border-color: #e5e7eb; }
.leader-popup-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
    margin: 0.5rem 0;
}
.leader-popup-workload-title {
    font-size: 0.7rem; font-weight: 600; color: #6b7280;
    display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.5rem;
}
.leader-popup-workload-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem;
}
.leader-popup-workload-item {
    text-align: center; padding: 0.4rem 0.25rem;
    background: #f8fafc; border-radius: 0.5rem; border: 1px solid #f1f5f9;
}
.leader-popup-workload-item .w-total,
.leader-popup-workload-item .w-pending,
.leader-popup-workload-item .w-completed {
    display: block; font-size: 0.95rem; font-weight: 700; line-height: 1;
}
.leader-popup-workload-item .w-total { color: #1a2a4a; }
.leader-popup-workload-item .w-pending { color: #d97706; }
.leader-popup-workload-item .w-completed { color: #059669; }
.leader-popup-workload-item .w-label {
    display: block; font-size: 0.55rem; color: #9ca3af;
    text-transform: uppercase; letter-spacing: 0.03em; margin-top: 0.2rem;
}

.leader-popup-breakdown {
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
.btn-secondary:hover { background: #e5e7eb; color: #1f2937; transform: translateY(-1px); }

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
@endphp

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-6">
        <span class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-lg shadow-md">
            <i class="fas fa-diagram-project"></i>
        </span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Add Sub-task</h2>
            <p class="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                <i class="fas fa-clipboard-list text-blue-400 text-xs"></i>
                Under: {{ $parentTask->title }}
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.subtask.store', $parentTask) }}" id="subtaskForm">
        @csrf

        {{-- SECTION 1: Sub-task Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                Sub-task Details
            </div>

            <div class="mb-4">
                <label class="form-label">Sub-task Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="form-input"
                       placeholder="e.g., Draft foundation plan">
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3"
                          class="form-textarea"
                          placeholder="Brief description...">{{ old('description') }}</textarea>
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
                    <span class="skill-chip" data-skill="{{ $skill }}">{{ $skill }}</span>
                @endforeach
            </div>
            <div id="skillsHiddenInputs"></div>
        </div>

        {{-- SECTION 3: Department & Schedule --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                Department & Schedule
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <div class="lg:col-span-2">
                    <label class="form-label">Department <span class="text-red-500">*</span></label>
                    <select name="department_id" id="departmentSelect" required class="form-select">
                        <option value="">Select...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->members_count }})
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="lg:col-span-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                   class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Due Date <span class="text-red-500">*</span></label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" required
                                   class="form-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: Assign To --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-user-check"></i></span>
                Assign To
            </div>
            <div id="membersContainer">
                <div class="text-center py-10 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-dashed border-gray-200">
                    <i class="fas fa-user-check text-2xl text-gray-300 mb-2 block"></i>
                    <p class="text-sm text-gray-400">Pick a department to see available members</p>
                </div>
            </div>
            <input type="hidden" name="assigned_to" id="assignedToInput" value="{{ old('assigned_to') }}">
            @error('assigned_to') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="sm:w-40">
                <label class="form-label mb-1">Priority</label>
                <select name="priority" class="form-select text-sm">
                    <option value="low"    {{ old('priority') === 'low'    ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high"   {{ old('priority') === 'high'   ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <div class="flex-1"></div>
            <a href="{{ route('tasks.show', $parentTask) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-plus"></i> Create Sub-task
            </button>
        </div>
    </form>
</div>

{{-- HOVER POPUP --}}
<div id="leaderPopup" class="leader-popup">
    <div class="leader-popup-inner">
        <div class="leader-popup-header">
            <img id="popupAvatar" src="" alt="" class="leader-popup-avatar">
            <div class="min-w-0 flex-1">
                <p id="popupName" class="leader-popup-name"></p>
                <p id="popupEmail" class="leader-popup-email"></p>
            </div>
            <span id="popupStatusBadge" class="leader-popup-status"></span>
        </div>

        <div class="leader-popup-body">
            <div class="leader-popup-row">
                <span class="leader-popup-label"><i class="fas fa-briefcase"></i> Job Scope</span>
                <span id="popupJobScope" class="leader-popup-value"></span>
            </div>
            <div class="leader-popup-row">
                <span class="leader-popup-label"><i class="fas fa-trophy"></i> AI Score</span>
                <span id="popupScore" class="leader-popup-value"></span>
            </div>

            <div class="leader-popup-row" id="popupMatchedRow">
                <span class="leader-popup-label"><i class="fas fa-check-circle"></i> Matches</span>
                <div id="popupMatched" class="leader-popup-tags"></div>
            </div>
            <div class="leader-popup-row" id="popupMissingRow">
                <span class="leader-popup-label"><i class="fas fa-times-circle"></i> Missing</span>
                <div id="popupMissing" class="leader-popup-tags"></div>
            </div>

            <div class="leader-popup-row" id="popupBreakdownRow" style="display:none;">
                <div style="width:100%;">
                    <span class="leader-popup-label" style="margin-bottom:0.35rem;">
                        <i class="fas fa-calculator"></i> Score Breakdown
                    </span>
                    <div class="leader-popup-breakdown" id="popupBreakdown"></div>
                </div>
            </div>

            <div class="leader-popup-divider"></div>
            <div class="leader-popup-workload-title">
                <i class="fas fa-chart-bar"></i> Workload
            </div>
            <div class="leader-popup-workload-grid">
                <div class="leader-popup-workload-item">
                    <span class="w-total" id="popupTotal">0</span>
                    <span class="w-label">Total</span>
                </div>
                <div class="leader-popup-workload-item">
                    <span class="w-pending" id="popupPending">0</span>
                    <span class="w-label">Pending</span>
                </div>
                <div class="leader-popup-workload-item">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form          = document.getElementById('subtaskForm');
    const deptSelect    = document.getElementById('departmentSelect');
    const membersBox    = document.getElementById('membersContainer');
    const assignedInput = document.getElementById('assignedToInput');
    const skillPicker   = document.getElementById('skillPicker');
    const hiddenBox     = document.getElementById('skillsHiddenInputs');

    const popup           = document.getElementById('leaderPopup');
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

    const selectedSkills = new Set();
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

    form.addEventListener('submit', function (e) {
        hiddenBox.innerHTML = '';
        selectedSkills.forEach(function (skill) {
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'skills_required[]';
            h.value = skill;
            hiddenBox.appendChild(h);
        });
        if (!assignedInput.value) {
            e.preventDefault();
            alert('Please select a member for this sub-task.');
        }
    });

    function setEmpty() {
        membersBox.innerHTML = `
            <div class="text-center py-8 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-dashed border-gray-200">
                <i class="fas fa-user-check text-2xl text-gray-300 mb-2 block"></i>
                <p class="text-sm text-gray-400">Pick a department to see available members</p>
            </div>`;
        assignedInput.value = '';
    }

    deptSelect.addEventListener('change', function () {
        const deptId = this.value;
        if (!deptId) { setEmpty(); return; }

        membersBox.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                <p class="text-sm text-gray-400 mt-2">Analyzing workloads...</p>
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
                    assignedInput.value = '';
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
            const rec = m.is_recommended ? 'recommended' : '';
            const wc  = m.workload_color;
            const pct = m.workload_percent;
            let skillTags = '';
            (m.matched_skills || []).forEach(s => skillTags += `<span class="skill-tag matched">${s}</span>`);
            (m.missing_skills || []).forEach(s => skillTags += `<span class="skill-tag missing">${s}</span>`);

            const opt = document.createElement('div');
            opt.className = `leader-option ${rec}`;
            opt.dataset.userId = m.id;
            opt.dataset.member = JSON.stringify(m);
            opt.innerHTML = `
                <img src="${m.profile_picture}" class="w-10 h-10 rounded-full object-cover border border-gray-200" alt="">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-gray-900 truncate">${m.name}</p>
                        <span class="text-[0.6rem] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">${m.job_scope ?? 'No job'}</span>
                    </div>
                    <div class="flex items-center gap-3 mt-1 text-[0.65rem] text-gray-500">
                        <span><i class="fas fa-tasks text-amber-400"></i> ${m.pending_tasks} pending</span>
                        <span><i class="fas fa-check-circle text-emerald-400"></i> ${m.completed_tasks} done</span>
                        <span class="ml-auto">
                            <span class="workload-percent ${wc}">${pct}%</span>
                            <span class="text-gray-400">· ${m.workload_label}</span>
                        </span>
                    </div>
                    <div class="workload-track">
                        <div class="workload-fill workload-${wc}" style="width:${pct}%"></div>
                    </div>
                    ${skillTags ? `<div class="mt-1">${skillTags}</div>` : ''}
                </div>
            `;
            membersBox.appendChild(opt);
        });

        membersBox.querySelectorAll('.leader-option').forEach(el => {
            el.addEventListener('click', function () {
                membersBox.querySelectorAll('.leader-option').forEach(x => x.classList.remove('selected'));
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

        const rec = membersBox.querySelector('.leader-option.recommended');
        if (rec) {
            rec.classList.add('selected');
            assignedInput.value = rec.dataset.userId;
        }
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
            t.className = 'leader-popup-tag matched';
            t.textContent = s;
            popupMatched.appendChild(t);
        });
        if (!m.matched_skills || !m.matched_skills.length) {
            popupMatched.innerHTML = '<span class="text-xs text-gray-400">None</span>';
        }

        popupMissing.innerHTML = '';
        (m.missing_skills || []).forEach(s => {
            const t = document.createElement('span');
            t.className = 'leader-popup-tag missing';
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

    if (deptSelect.value) deptSelect.dispatchEvent(new Event('change'));
});
</script>

@endsection