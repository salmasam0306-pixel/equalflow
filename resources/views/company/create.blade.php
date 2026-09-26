@extends('layouts.app')

@section('title', 'Create Organisation')

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

/* ============================================ */
/* ICON HEADER (centered variant)                */
/* ============================================ */
.icon-header {
    text-align: center;
    margin-bottom: 1.5rem;
}
.icon-header .icon-wrapper {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.85rem;
    font-size: 1.5rem;
    color: #1a2a4a;
    border: 2px solid #bfdbfe;
    box-shadow: 0 4px 14px rgba(26, 42, 74, 0.08);
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
    padding: 0.85rem 1.75rem;
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
    width: 100%;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(26, 42, 74, 0.28);
}

.footer-link {
    margin-top: 1.5rem;
    padding-top: 1.25rem;
    border-top: 1px solid #e5e7eb;
    text-align: center;
    font-size: 0.8rem;
    color: #6b7280;
}
.footer-link a {
    color: #1a2a4a;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.footer-link a:hover {
    color: #0f1a30;
    text-decoration: underline;
}
</style>

<div class="max-w-2xl mx-auto px-4 py-6">

    {{-- Centered Icon Header --}}
    <div class="icon-header">
        <div class="icon-wrapper">
            <i class="fas fa-building"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-900">Create Your Organisation</h2>
        <p class="text-sm text-gray-500 mt-1">Set up your organisation workspace and start collaborating.</p>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
        <div class="mb-5 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="mb-5 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('company.store') }}">
        @csrf

        {{-- SECTION 1: Organisation Details --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-building"></i></span>
                Organisation Details
            </div>

            <div class="mb-4">
                <label class="form-label">Organisation Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="form-input"
                       placeholder="e.g., C&S Engineering Firm">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">Industry</label>
                <input type="text" name="industry" value="{{ old('industry') }}"
                       class="form-input"
                       placeholder="e.g., Engineering, Construction">
                @error('industry')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3"
                          class="form-textarea"
                          placeholder="Brief description of your organisation">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- ACTION BUTTON --}}
        <button type="submit" class="btn-primary">
            <i class="fas fa-building"></i> Create Organisation
        </button>
    </form>

    {{-- Footer Link --}}
    <div class="footer-link">
        Already have an organisation?
        <a href="{{ route('company.join') }}">
            Join with invite code <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>

@endsection