{{-- resources/views/departments/assign-department.blade.php --}}
@extends('layouts.app')

@section('title', 'Assign Department & Skills')

@section('content')

<style>
/* ============================================ */
/* BASE FORM CONTROLS                            */
/* ============================================ */
.form-input, .form-select {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover, .form-select:hover { border-color: #cbd5e1; }
.form-input:focus, .form-select:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder { color: #9ca3af; }
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.9rem center;
    background-size: 12px;
    padding-right: 2.2rem;
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
/* PAGE HEADER                                   */
/* ============================================ */
.page-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 1.5rem;
}
.page-header .icon {
    width: 44px;
    height: 44px;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #10b981, #0d9488);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    flex-shrink: 0;
}

/* ============================================ */
/* USER PROFILE CARD                             */
/* ============================================ */
.user-profile {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    border: 1px solid #e5e7eb;
    margin-bottom: 1rem;
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
/* DEPARTMENT RADIO OPTIONS                      */
/* ============================================ */
.dept-option {
    transition: all 0.18s ease;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.85rem;
    padding: 1rem 1.15rem;
    cursor: pointer;
    background: #fff;
    position: relative;
    display: block;
}
.dept-option:hover {
    border-color: #94a3b8;
    transform: translateY(-1px);
}
.dept-option:has(input:checked) {
    border-color: #1a2a4a;
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    box-shadow: 0 4px 14px rgba(26, 42, 74, 0.08);
}
.dept-option input { display: none; }
.dept-option .check-mark {
    position: absolute;
    top: 0.8rem;
    right: 0.8rem;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    font-size: 0.65rem;
    transition: all 0.2s ease;
}
.dept-option:has(input:checked) .check-mark {
    background: #1a2a4a;
    color: #fff;
}

/* ============================================ */
/* SKILL PICKER                                  */
/* ============================================ */
.skill-picker {
    display: flex;
    flex-wrap: wrap;
    gap: 0.45rem;
    padding: 0.9rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.65rem;
    background: #fafbfc;
    max-height: 260px;
    overflow-y: auto;
}
.skill-picker::-webkit-scrollbar { width: 5px; }
.skill-picker::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

.skill-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.85rem;
    border-radius: 9999px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #64748b;
    font-size: 0.78rem;
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
/* INFO BOX                                      */
/* ============================================ */
.info-box {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 0.75rem;
    padding: 1rem 1.15rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.info-box i { color: #3b82f6; }

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
    text-decoration: none;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
    color: #fff;
}
.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
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
    $contextDeptId = $contextDepartmentId ?? null;
    $currentDeptId = old('department_id', $contextDeptId ?: $user->department_id);
    $currentJobScope    = old('job_scope', $user->job_scope);
    $currentSpecialties = old('specialties', $user->specialties ?? []);

    $jobScopeOptions = \App\Models\User::getJobScopeLabels();

    $specialtyOptions = [
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

    $fromDepartment = $fromDepartment ?? false;
@endphp

<div class="max-w-3xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="page-header">
        <span class="icon"><i class="fas fa-user-tag"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">
                {{ $fromDepartment ? 'Edit Member Profile' : 'Assign Department & Skills' }}
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Set the department, job scope and specialties for this member.
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- User card --}}
    <div class="user-profile">
        <div class="flex items-center gap-4">
            <img src="{{ $user->getProfilePictureUrl() }}"
                 alt="{{ $user->name }}"
                 class="w-14 h-14 rounded-full object-cover border-2 border-indigo-200 shadow-sm">
            <div class="flex-1 min-w-0">
                <p class="text-base font-semibold text-gray-900 flex items-center gap-2">
                    {{ $user->name }}
                    <span class="text-xs px-2.5 py-0.5 rounded-full
                        @if($user->role === 'leader') bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ ucfirst($user->role) }}
                    </span>
                </p>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                @if($user->department)
                    <p class="text-xs text-gray-400 mt-0.5">
                        <i class="fas fa-building mr-1"></i> Current: {{ $user->department->name }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <form method="POST"
          action="{{ route('departments.assign.department.update', $user) }}"
          id="assignForm">
        @csrf
        @method('PUT')

        @if($fromDepartment)
            <input type="hidden" name="from" value="department">
        @endif

        {{-- SECTION 1: Department --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-building"></i></span>
                Department
                <span class="section-hint">
                    Required — the member will be moved here
                </span>
            </div>

            @if($departments->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($departments as $department)
                        <label class="dept-option">
                            <input type="radio" name="department_id" value="{{ $department->id }}"
                                   {{ (int) $currentDeptId === $department->id ? 'checked' : '' }}>
                            <span class="check-mark"><i class="fas fa-check"></i></span>
                            <p class="font-medium text-gray-900 text-sm">{{ $department->name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $department->members()->count() }} members
                            </p>
                        </label>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-sm text-gray-400">No departments available.</p>
                    <a href="{{ route('departments.create') }}"
                       class="mt-3 inline-block text-xs text-[#1a2a4a] hover:text-[#0f1a30] font-medium">
                        Create a department →
                    </a>
                </div>
            @endif

            @error('department_id')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- SECTION 2: Job Scope --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-briefcase"></i></span>
                Job Scope
                <span class="section-hint">
                    Determines engineering eligibility + baseline skills
                </span>
            </div>
            <select name="job_scope" class="form-select">
                <option value="">— Select a job scope —</option>
                @foreach($jobScopeOptions as $key => $label)
                    <option value="{{ $key }}" {{ $currentJobScope === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('job_scope')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- SECTION 3: Specialties --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-tags"></i></span>
                Specialties
                <span class="section-hint">
                    Used by AI for task matching
                </span>
            </div>

            <div class="skill-picker" id="skillPicker">
                @foreach($specialtyOptions as $skill)
                    <span class="skill-chip {{ in_array($skill, $currentSpecialties) ? 'selected' : '' }}"
                          data-skill="{{ $skill }}">{{ $skill }}</span>
                @endforeach
            </div>

            <div id="skillsHiddenInputs"></div>

            @error('specialties')
                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- INFO BOX --}}
        <div class="info-box">
            <i class="fas fa-lightbulb text-lg mt-0.5"></i>
            <div>
                <p class="font-semibold text-blue-800 text-sm">What happens next?</p>
                <p class="text-blue-700 text-xs mt-1 leading-relaxed">
                    The member will be moved to this department, and their profile will show
                    the job scope and specialties you set here. They cannot change them.
                </p>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="flex-1"></div>
            <a href="{{ $fromDepartment
                        ? route('departments.show', $contextDepartmentId ?: $user->department_id)
                        : route('departments.index') }}"
               class="btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn-primary"
                    {{ $departments->count() === 0 ? 'disabled' : '' }}>
                <i class="fas fa-check-circle"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form        = document.getElementById('assignForm');
    const skillPicker = document.getElementById('skillPicker');
    const hiddenBox   = document.getElementById('skillsHiddenInputs');

    skillPicker.querySelectorAll('.skill-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            this.classList.toggle('selected');
        });
    });

    form.addEventListener('submit', function () {
        hiddenBox.innerHTML = '';
        skillPicker.querySelectorAll('.skill-chip.selected').forEach(function (chip) {
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'specialties[]';
            h.value = chip.dataset.skill;
            hiddenBox.appendChild(h);
        });
    });
});
</script>

@endsection