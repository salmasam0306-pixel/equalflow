@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')

<style>
/* ===== CARD STYLES ===== */
.verify-card {
    background: #ffffff;
    border-radius: 1.5rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    padding: 2rem;
    transition: all 0.3s ease;
}

.verify-card:hover {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
}

/* ===== ICON WRAPPER ===== */
.icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 2.5rem;
    color: #1a2a4a;
    border: 2px solid #bfdbfe;
    box-shadow: 0 4px 20px rgba(26, 42, 74, 0.08);
}

/* ===== BUTTONS ===== */
.btn-primary {
    background: linear-gradient(135deg, #1a2a4a 0%, #2d4a7a 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0f1a30 0%, #1a2a4a 100%);
    box-shadow: 0 4px 15px rgba(26, 42, 74, 0.3);
    transform: translateY(-1px);
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #e5e7eb;
    transform: translateY(-1px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 640px) {
    .verify-card {
        padding: 1.5rem;
    }
    .icon-wrapper {
        width: 60px;
        height: 60px;
        font-size: 1.75rem;
    }
}
</style>

<div class="max-w-md mx-auto px-4">
    <div class="verify-card">
        {{-- Logo --}}
        <div class="text-center mb-6">
            <a href="/" class="text-2xl font-extrabold text-gray-900 flex items-center justify-center gap-2">
                <svg class="w-7 h-7 text-[#1a2a4a]" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
                Equal<span class="text-[#1a2a4a]">Flow</span>
            </a>
        </div>

        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="icon-wrapper">
                <i class="fas fa-envelope"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Verify Your Email</h2>
            <p class="text-sm text-gray-500 mt-1">
                We've sent a verification link to your email address.
            </p>
        </div>

        {{-- Success Alert --}}
        @if(session('resent'))
            <div class="mb-4 p-4 bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl text-sm flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
                A new verification link has been sent to your email address.
            </div>
        @endif

        {{-- Warning Box --}}
        <div class="p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl text-sm mb-6 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 text-lg mt-0.5"></i>
            <div>
                <p class="font-medium text-amber-700">Please check your email</p>
                <p class="text-amber-600 text-xs mt-0.5">
                    Click the verification link in the email to activate your account.
                </p>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-6 border border-gray-200">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-1">
                <i class="fas fa-info-circle text-blue-400"></i> What to do next
            </h4>
            <ul class="space-y-1.5 text-sm text-gray-600">
                <li class="flex items-start gap-2">
                    <span class="text-emerald-500 text-xs mt-0.5"><i class="fas fa-check-circle"></i></span>
                    <span>Check your email inbox</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-emerald-500 text-xs mt-0.5"><i class="fas fa-check-circle"></i></span>
                    <span>Click the verification link in the email</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-emerald-500 text-xs mt-0.5"><i class="fas fa-check-circle"></i></span>
                    <span>You'll be redirected to the dashboard</span>
                </li>
            </ul>
        </div>

        {{-- Resend Button --}}
        <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
            @csrf
            <button type="submit" class="btn-primary w-full inline-flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i> Resend Verification Email
            </button>
        </form>

        {{-- Logout Button --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn-secondary w-full inline-flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>

        {{-- Footer Note --}}
        <p class="mt-4 text-center text-xs text-gray-400">
            <i class="fas fa-clock mr-1"></i> 
            Didn't receive the email? Check your spam folder or click the button above to resend.
        </p>
    </div>
</div>

@endsection