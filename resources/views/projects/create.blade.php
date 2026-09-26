{{-- resources/views/projects/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Project')

@section('content')

<style>
.form-input {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
}
.form-input:hover { border-color: #cbd5e1; }
.form-input:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder { color: #9ca3af; }

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
    background: linear-gradient(135deg, #f97316, #dc2626);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.25);
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

.info-box {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 0.75rem;
    padding: 0.9rem 1.1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.7rem;
    font-size: 0.8rem;
    color: #1e40af;
    line-height: 1.4;
    margin-bottom: 1rem;
}
.info-box i { color: #3b82f6; flex-shrink: 0; }

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
        <span class="icon"><i class="fas fa-hard-hat"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Create New Project</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                @if(auth()->user()->isPrincipal())
                    Create a company project and add tasks under it.
                @else
                    Create a new company project.
                @endif
            </p>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('projects.store') }}">
        @csrf
        <input type="hidden" name="type" value="company">

        {{-- SECTION 1: Project Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-file-alt"></i></span>
                Project Details
            </div>

            <div class="mb-4">
                <label class="form-label">Project Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="form-input @error('name') border-red-500 @enderror"
                       placeholder="e.g., Taman Mutiara Drainage Upgrade">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="4"
                          class="form-input @error('description') border-red-500 @enderror"
                          placeholder="Project description">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SECTION 2: Schedule --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                Schedule
                <span class="section-hint">Deadline must be on or after start date</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                           class="form-input @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                           class="form-input @error('deadline') border-red-500 @enderror">
                    @error('deadline')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- INFO BOX --}}
        @if(auth()->user()->isPrincipal())
            <div class="info-box">
                <i class="fas fa-lightbulb text-lg"></i>
                <div>
                    <p class="font-semibold">Note</p>
                    <p class="mt-0.5 text-xs">After creating this project, you can add <strong>Main Tasks</strong> and assign a leader to each one.</p>
                </div>
            </div>
        @endif

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="flex-1"></div>
            <a href="{{ route('projects.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-plus-circle"></i> Create Project
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startInput = document.getElementById('start_date');
    const endInput   = document.getElementById('deadline');

    if (!startInput || !endInput) return;

    function syncMin() {
        if (startInput.value) {
            endInput.min = startInput.value;
            if (endInput.value && endInput.value < startInput.value) {
                endInput.value = '';
            }
        } else {
            endInput.removeAttribute('min');
        }
    }

    startInput.addEventListener('change', syncMin);
    syncMin();
});
</script>

@endsection