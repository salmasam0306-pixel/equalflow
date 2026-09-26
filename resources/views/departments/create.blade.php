{{-- resources/views/departments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Department')

@section('content')

<style>
.form-input, .form-textarea {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover, .form-textarea:hover { border-color: #cbd5e1; }
.form-input:focus, .form-textarea:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder, .form-textarea::placeholder { color: #9ca3af; }

.form-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-bottom: 0.45rem;
}

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
    background: linear-gradient(135deg, #3b82f6, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
    flex-shrink: 0;
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
    letter-spacing: 0;
    text-transform: none;
}

/* ============================================ */
/* MEMBER CHECKBOX ITEM                          */
/* ============================================ */
.member-checkbox-item {
    transition: all 0.15s ease;
    border: 1.5px solid #e5e7eb;
    border-radius: 0.65rem;
    padding: 0.75rem 0.9rem;
    cursor: pointer;
    background: #fff;
    display: flex;
    align-items: center;
    gap: 0.85rem;
}
.member-checkbox-item:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}
.member-checkbox-item:has(input:checked) {
    border-color: #1a2a4a;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}
.member-checkbox-item input { accent-color: #1a2a4a; width: 16px; height: 16px; }

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
/* BUTTONS + ACTION BAR                          */
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

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="page-header">
        <span class="icon"><i class="fas fa-building"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Create Department</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $org->name ?? 'Company' }}</p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('departments.store') }}">
        @csrf

        {{-- SECTION 1: Department Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                Department Details
            </div>

            <div class="mb-4">
                <label class="form-label">Department Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="form-input"
                       placeholder="e.g., Civil Engineering">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3"
                          class="form-textarea"
                          placeholder="Department description...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SECTION 2: Assign Members --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-users"></i></span>
                Assign Members
                <span class="section-hint">Optional — can add more later</span>
            </div>

            <div class="grid grid-cols-1 gap-2 max-h-80 overflow-y-auto pr-1">
                @forelse($availableMembers as $member)
                    <label class="member-checkbox-item">
                        <input type="checkbox" name="members[]" value="{{ $member->id }}">
                        <img src="{{ $member->getProfilePictureUrl() }}"
                             alt="{{ $member->name }}"
                             class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $member->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $member->email }}</p>
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0">
                            {{ $member->job_scope ?? 'No job' }}
                        </span>
                    </label>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">
                        No members available to assign.
                    </p>
                @endforelse
            </div>

            <p class="text-xs text-gray-500 mt-3 flex items-center gap-1">
                <i class="fas fa-check-circle text-emerald-500"></i>
                Selected: <span id="selectedCount" class="font-semibold text-gray-800">0</span> members
            </p>
        </div>

        {{-- INFO BOX --}}
        <div class="info-box">
            <i class="fas fa-lightbulb text-lg mt-0.5"></i>
            <div>
                <p class="font-semibold text-blue-800 text-sm">Note</p>
                <p class="text-blue-700 text-xs mt-1 leading-relaxed">
                    Departments are permanent teams within your company. Members can belong to one department.
                    Projects will pull members from these departments.
                </p>
            </div>
        </div>

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="flex-1"></div>
            <a href="{{ route('departments.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Create Department
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="members[]"]');
        const selectedCount = document.getElementById('selectedCount');

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checked = document.querySelectorAll('input[name="members[]"]:checked');
                selectedCount.textContent = checked.length;
            });
        });
    });
</script>

@endsection