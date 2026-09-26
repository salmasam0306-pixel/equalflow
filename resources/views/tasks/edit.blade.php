{{-- resources/views/tasks/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Main Task')

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
    letter-spacing: -0.005em;
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
/* LEADER OPTION                                 */
/* ============================================ */
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
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
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
    top: -8px;
    right: 12px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    font-size: 0.55rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
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

.btn-danger {
    background: #fef2f2;
    color: #dc2626;
    padding: 0.65rem 1.15rem;
    border-radius: 0.7rem;
    border: 1px solid #fca5a5;
    font-weight: 500;
    font-size: 0.8rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: all 0.15s ease;
}
.btn-danger:hover {
    background: #fee2e2;
    border-color: #dc2626;
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
@endphp

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-6">
        <span class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white text-lg shadow-md">
            <i class="fas fa-pen"></i>
        </span>
        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900">Edit Main Task</h2>
            <p class="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                <i class="fas fa-folder text-blue-400 text-xs"></i>
                {{ $task->project->name }}
            </p>
        </div>
        @if(auth()->user()->isPrincipal())
            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                  onsubmit="return confirm('Delete this main task and all its sub-tasks?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </form>
        @endif
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task) }}" id="editTaskForm">
        @csrf
        @method('PUT')

        {{-- SECTION 1: Task Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                Task Details
            </div>

            <div class="mb-4">
                <label class="form-label">Task Title <span class="text-red-500">*</span></label>
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
                <span class="section-hint">Used by AI to match the best leader</span>
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
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                <div class="lg:col-span-2">
                    <label class="form-label">Department</label>
                    <select name="department_id" id="departmentSelect" class="form-select">
                        <option value="">— Standalone —</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department_id', $task->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="lg:col-span-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="startDateInput"
                                   value="{{ old('start_date', $task->start_date?->format('Y-m-d')) }}"
                                   class="form-input">
                            <p id="startDateHint" class="mt-1 text-xs text-gray-400 hidden"></p>
                            @error('start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date"
                                   value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                                   class="form-input">
                            @error('due_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 4: Assigned Leader --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-user-tie"></i></span>
                Assigned Leader
                <span class="section-hint">Only engineers are eligible</span>
            </div>
            <div id="leadersContainer">
                <div class="text-center py-10 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-400">Loading...</p>
                </div>
            </div>
            <input type="hidden" name="assigned_to" id="assignedToInput"
                   value="{{ old('assigned_to', $task->assigned_to) }}">
            @error('assigned_to') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- SECTION 5: Dependencies (conditional) --}}
        @if(isset($siblingMainTasks) && $siblingMainTasks->count() > 0)
            <div class="form-section">
                <div class="section-title">
                    <span class="icon"><i class="fas fa-link"></i></span>
                    Dependencies
                    <span class="section-hint">Blocks this task until selected are done</span>
                </div>
                <div class="max-h-48 overflow-y-auto">
                    @foreach($siblingMainTasks as $sib)
                        <label class="dep-item">
                            <input type="checkbox" name="dependencies[]" value="{{ $sib->id }}"
                                   data-status="{{ $sib->status }}"
                                   data-due="{{ $sib->due_date?->format('Y-m-d') ?? '' }}"
                                   {{ in_array($sib->id, old('dependencies', $currentDependencies ?? [])) ? 'checked' : '' }}>
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
            <div class="sm:w-40">
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
            <a href="{{ route('tasks.show', $task) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form          = document.getElementById('editTaskForm');
    const deptSelect    = document.getElementById('departmentSelect');
    const leadersBox    = document.getElementById('leadersContainer');
    const assignedInput = document.getElementById('assignedToInput');
    const skillPicker   = document.getElementById('skillPicker');
    const hiddenBox     = document.getElementById('skillsHiddenInputs');

    const startDateInput = document.getElementById('startDateInput');
    const startDateHint  = document.getElementById('startDateHint');
    let   smartDateTimer = null;

    const selectedSkills = new Set(
        Array.from(skillPicker.querySelectorAll('.skill-chip.selected'))
             .map(el => el.dataset.skill)
    );

    function refreshLeaders() {
        if (deptSelect.value) deptSelect.dispatchEvent(new Event('change'));
        else setEmpty();
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
            refreshLeaders();
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
        leadersBox.innerHTML = `
            <div class="text-center py-8 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-sm text-gray-400">Pick a department to see available leaders</p>
            </div>`;
        assignedInput.value = '';
    }

    deptSelect.addEventListener('change', function () {
        const deptId = this.value;
        if (!deptId) { setEmpty(); return; }

        leadersBox.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-gray-400"></i>
            </div>`;

        const params = new URLSearchParams();
        selectedSkills.forEach(s => params.append('skills[]', s));
        params.append('engineers_only', '1');

        const url = `/tasks/departments/${deptId}/members-with-workload?${params.toString()}`;

        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(async r => {
                if (!r.ok) throw new Error(`HTTP ${r.status}`);
                return r.json();
            })
            .then(data => {
                if (!data.success || !data.members || data.members.length === 0) {
                    leadersBox.innerHTML = `
                        <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                            <p class="text-sm text-gray-400">No members in this department</p>
                        </div>`;
                    return;
                }
                renderLeaders(data.members);
            })
            .catch(err => {
                leadersBox.innerHTML = `
                    <div class="text-center py-6 bg-red-50 rounded-lg border border-red-200">
                        <p class="text-sm text-red-500 font-medium">Failed to load members.</p>
                        <p class="text-xs text-red-400 mt-1">${err.message}</p>
                    </div>`;
            });
    });

    function renderLeaders(members) {
        leadersBox.innerHTML = '';
        members.forEach(function (m) {
            const isCurrent = String(m.id) === String(assignedInput.value);
            const rec = m.is_recommended && !isCurrent ? 'recommended' : '';
            const sel = isCurrent ? 'selected' : '';
            const wc  = m.workload_color;
            const pct = m.workload_percent;
            let skillTags = '';
            (m.matched_skills || []).forEach(s => skillTags += `<span class="skill-tag matched">${s}</span>`);
            (m.missing_skills || []).forEach(s => skillTags += `<span class="skill-tag missing">${s}</span>`);

            leadersBox.insertAdjacentHTML('beforeend', `
                <div class="leader-option ${rec} ${sel}" data-user-id="${m.id}">
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
                </div>`);
        });

        leadersBox.querySelectorAll('.leader-option').forEach(el => {
            el.addEventListener('click', function () {
                leadersBox.querySelectorAll('.leader-option').forEach(x => x.classList.remove('selected'));
                this.classList.add('selected');
                assignedInput.value = this.dataset.userId;
            });
        });
    }

    // ============================================
    // SMART START DATE
    // ============================================
    async function recomputeSmartStartDate() {
        if (!startDateInput || !startDateHint) return;

        const checked = Array.from(document.querySelectorAll('input[name="dependencies[]"]:checked'))
                             .map(el => el.value);

        if (checked.length === 0) {
            startDateHint.classList.add('hidden');
            startDateInput.removeAttribute('min');
            return;
        }

        const params = new URLSearchParams();
        checked.forEach(id => params.append('dependencies[]', id));
        const url = `/tasks/smart-date-preview?${params.toString()}`;

        try {
            const r = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const data = await r.json();

            if (data.all_done) {
                startDateInput.removeAttribute('min');
                startDateHint.textContent = '✅ All blocking tasks are done — you can start any time.';
                startDateHint.className = 'mt-1 text-xs text-emerald-600 flex items-center gap-1';
                startDateHint.classList.remove('hidden');
            } else if (data.recommended_start_date) {
                startDateInput.setAttribute('min', data.recommended_start_date);
                startDateHint.textContent = `🔒 Blocked until ${data.latest_due_date} — earliest start ${data.recommended_start_date}.`;
                startDateHint.className = 'mt-1 text-xs text-amber-600 flex items-center gap-1';
                startDateHint.classList.remove('hidden');
            } else {
                startDateInput.removeAttribute('min');
                startDateHint.textContent = '⚠️ Blocking tasks have no due dates — pick a start date manually.';
                startDateHint.className = 'mt-1 text-xs text-amber-600 flex items-center gap-1';
                startDateHint.classList.remove('hidden');
            }
        } catch (err) {
            startDateHint.textContent = 'Could not compute recommended start date.';
            startDateHint.className = 'mt-1 text-xs text-gray-400';
            startDateHint.classList.remove('hidden');
        }
    }

    document.querySelectorAll('input[name="dependencies[]"]').forEach(cb => {
        cb.addEventListener('change', () => {
            clearTimeout(smartDateTimer);
            smartDateTimer = setTimeout(recomputeSmartStartDate, 250);
        });
    });

    if (document.querySelector('input[name="dependencies[]"]:checked')) {
        recomputeSmartStartDate();
    }

    // INIT
    if (deptSelect.value) {
        deptSelect.dispatchEvent(new Event('change'));
    } else if (assignedInput.value) {
        leadersBox.innerHTML = `
            <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                <p class="text-sm text-gray-400">Standalone task — current assignee kept on save</p>
            </div>`;
    }
});
</script>

@endsection