{{-- resources/views/departments/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Department')

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
.form-input:hover, .form-select:hover, .form-textarea:hover { border-color: #cbd5e1; }
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
        <span class="icon"><i class="fas fa-edit"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Edit Department</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $department->name }}</p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('departments.update', $department) }}">
        @csrf
        @method('PUT')

        {{-- SECTION 1: Department Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                Department Details
            </div>

            <div class="mb-4">
                <label class="form-label">Department Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $department->name) }}" required
                       class="form-input">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3"
                          class="form-textarea">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SECTION 2: Status --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-toggle-on"></i></span>
                Status
                <span class="section-hint">
                    Inactive departments are hidden from new task assignments
                </span>
            </div>

            <select name="status" class="form-select">
                <option value="active"   {{ old('status', $department->status) === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $department->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="flex-1"></div>
            <a href="{{ route('departments.show', $department) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Update Department
            </button>
        </div>
    </form>
</div>

@endsection