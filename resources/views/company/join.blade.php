@extends('layouts.app')

@section('title', 'Join Organisation')

@section('content')

<style>
/* ===== CARD STYLES ===== */
.join-company-card {
    background: #ffffff;
    border-radius: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.join-company-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
}

/* ===== INPUT STYLES ===== */
.form-input {
    transition: all 0.3s ease;
    border-color: #e5e7eb;
    font-family: 'Courier New', monospace;
    letter-spacing: 0.1em;
}

.form-input:focus {
    border-color: #1a2a4a;
    box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1);
    outline: none;
}

.form-input:hover {
    border-color: #9ca3af;
}

.form-input::placeholder {
    font-family: 'Inter', sans-serif;
    letter-spacing: 0;
    color: #9ca3af;
}

/* ===== BUTTON ===== */
.btn-join {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    width: 100%;
}

.btn-join:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    box-shadow: 0 4px 15px rgba(26, 42, 74, 0.3);
    transform: translateY(-1px);
}

.btn-join:active {
    transform: scale(0.98);
}

/* ===== ICON WRAPPER ===== */
.icon-wrapper {
    width: 72px;
    height: 72px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2rem;
    color: #1a2a4a;
    border: 2px solid #bfdbfe;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 640px) {
    .join-company-card {
        padding: 1.5rem;
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
}
</style>

<div class="max-w-md mx-auto px-4">
    <div class="join-company-card p-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="icon-wrapper">
                <i class="fas fa-link"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Join an Organisation</h2>
            <p class="text-sm text-gray-500 mt-1">Enter your organisation's invite code to join.</p>
        </div>

        {{-- Alerts --}}
        @if(session('error'))
            <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-700 rounded-xl text-sm flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-500"></i>
                {{ session('info') }}
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('company.join.submit') }}">
            @csrf

            {{-- Invite Code --}}
            <div class="mb-6">
                <label for="invite_code" class="block text-sm font-semibold text-gray-700 mb-1">
                    Invite Code <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-key text-gray-400 text-sm"></i>
                    </div>
                    <input id="invite_code" type="text" name="invite_code" value="{{ old('invite_code') }}" required
                           class="form-input w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition @error('invite_code') border-red-500 @enderror uppercase"
                           placeholder="Enter invite code">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-[0.5rem] text-gray-400 font-medium uppercase">6-8 chars</span>
                    </div>
                </div>
                @error('invite_code')
                    <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
                <p class="mt-1 text-xs text-gray-400 flex items-center gap-1">
                    <i class="fas fa-info-circle"></i> 
                    Ask your organisation principal for the invite code
                </p>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="btn-join inline-flex items-center justify-center gap-2">
                <i class="fas fa-link"></i> Join Organisation
            </button>
        </form>

        {{-- Footer Link --}}
        <div class="mt-6 pt-6 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600">
                Don't have an organisation? 
                <a href="{{ route('company.create') }}" class="font-medium text-[#1a2a4a] hover:text-[#0f1a30] transition inline-flex items-center gap-1">
                    Create one <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </p>
        </div>
    </div>
</div>

@endsection