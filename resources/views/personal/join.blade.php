@extends('layouts.app')

@section('title', 'Join Personal Project')

@section('content')

<style>
/* ============================================ */
/* BASE FORM CONTROLS                            */
/* ============================================ */
.form-input {
    padding: 0.7rem 1rem;
    border-radius: 0.65rem;
    border: 1px solid #e5e7eb;
    background: #fafbfc;
    transition: all 0.18s ease;
    width: 100%;
    font-size: 0.875rem;
    color: #1f2937;
    font-family: 'Courier New', monospace;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.form-input:hover { border-color: #cbd5e1; }
.form-input:focus {
    background: #fff;
    border-color: #1a2a4a;
    box-shadow: 0 0 0 4px rgba(26, 42, 74, 0.08);
    outline: none;
}
.form-input::placeholder {
    font-family: 'Inter', sans-serif;
    letter-spacing: 0;
    text-transform: none;
    color: #9ca3af;
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
    background: linear-gradient(135deg, #6366f1, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    flex-shrink: 0;
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
/* INVITE CODE INPUT                             */
/* ============================================ */
.invite-code-input-wrap {
    position: relative;
}
.invite-code-input-wrap .hint-label {
    position: absolute;
    top: 50%;
    right: 0.9rem;
    transform: translateY(-50%);
    font-size: 0.6rem;
    font-weight: 700;
    color: #94a3b8;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    pointer-events: none;
}

/* ============================================ */
/* INFO BOX                                      */
/* ============================================ */
.info-box {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 0.75rem;
    padding: 0.85rem 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    font-size: 0.75rem;
    color: #1e40af;
    line-height: 1.5;
    margin-bottom: 1rem;
}
.info-box i { color: #3b82f6; flex-shrink: 0; margin-top: 0.1rem; }

/* ============================================ */
/* BUTTONS + ACTION BAR                          */
/* ============================================ */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a, #2d4a7a);
    color: #fff;
    padding: 0.75rem 1.5rem;
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

/* ============================================ */
/* FOOTER LINK                                   */
/* ============================================ */
.footer-link {
    margin-top: 1rem;
    text-align: center;
    font-size: 0.8rem;
    color: #64748b;
}
.footer-link a {
    color: #1a2a4a;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: color 0.15s ease;
}
.footer-link a:hover { color: #0f1a30; text-decoration: underline; }

@media (max-width: 640px) {
    .form-section { padding: 1rem; }
}
</style>

<div class="max-w-md mx-auto px-4 py-6">

    {{-- Page Header --}}
    <div class="page-header">
        <span class="icon"><i class="fas fa-user-plus"></i></span>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Join Personal Project</h2>
            <p class="text-sm text-gray-500 mt-0.5">Enter the project invite code to join.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm flex items-center gap-2">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('personal.join-by-code') }}">
        @csrf

        {{-- SECTION: Invite Code --}}
        <div class="form-section">
            <div class="section-title">
                <span class="icon"><i class="fas fa-key"></i></span>
                Invite Code
                <span class="section-hint">6 characters</span>
            </div>

            <div class="invite-code-input-wrap">
                <input id="invite_code"
                       type="text"
                       name="invite_code"
                       value="{{ old('invite_code') }}"
                       required
                       maxlength="6"
                       autocomplete="off"
                       autocapitalize="characters"
                       spellcheck="false"
                       placeholder="e.g., P5X9K2"
                       class="form-input @error('invite_code') border-red-500 @enderror">
                <span class="hint-label">6 chars</span>
            </div>

            @error('invite_code')
                <p class="mt-1 text-xs text-red-600 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- INFO BOX --}}
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <span>Ask the project creator for the invite code.</span>
        </div>

        {{-- ACTION BAR --}}
        <div class="action-bar">
            <div class="flex-1"></div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Join Project
            </button>
        </div>
    </form>

    {{-- Footer link --}}
    <div class="footer-link">
        Don't have an invite code?
        <a href="{{ route('personal.create') }}">
            Create your own project <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>

@endsection