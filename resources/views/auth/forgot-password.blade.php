@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="auth-form-container">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-key text-[#1a2a4a] text-xl"></i>
            Reset your password
        </h2>
        <p class="text-sm text-gray-500 mt-1.5">
            Enter your email address and we'll send you a link to reset your password.
        </p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2.5">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm space-y-1.5">
            @foreach($errors->all() as $error)
                <p class="flex items-start gap-2.5">
                    <i class="fas fa-exclamation-circle mt-0.5 text-red-500 text-xs"></i>
                    {{ $error }}
                </p>
            @endforeach
        </div>
    @endif

    <!-- Forgot Password Form -->
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Email Address
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                </div>
                <input id="email" 
                       type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       class="auth-input w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition @error('email') border-red-500 @enderror"
                       placeholder="you@company.com">
            </div>
            @error('email')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Info Box -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700 flex items-start gap-2.5">
            <i class="fas fa-info-circle text-blue-500 text-sm mt-0.5"></i>
            <div>
                <p class="font-medium">What happens next?</p>
                <p class="text-blue-600 text-xs mt-0.5">
                    We'll send you a password reset link to your email address. 
                    The link will expire in 60 minutes.
                </p>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-btn w-full py-3.5 px-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 group">
            <i class="fas fa-paper-plane group-hover:scale-110 transition-transform"></i>
            Send Reset Link
            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>

    <!-- Back to Login Link -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <p class="text-center text-sm text-gray-600">
            <a href="{{ route('login') }}" class="font-semibold text-[#1a2a4a] hover:text-[#0f1a30] transition inline-flex items-center gap-1 group">
                <i class="fas fa-arrow-left text-xs group-hover:-translate-x-0.5 transition-transform"></i>
                Back to Sign In
            </a>
        </p>
    </div>
</div>

<!-- Additional Styles -->
<style>
.auth-form-container {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-input {
    transition: all 0.3s ease;
}

.auth-input:focus {
    border-color: #1a2a4a;
    box-shadow: 0 0 0 3px rgba(26, 42, 74, 0.1);
    outline: none;
}

.auth-input::placeholder {
    color: #9ca3af;
    font-size: 0.875rem;
}

.auth-btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.auth-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1), transparent);
    transform: translateX(-100%);
    transition: transform 0.4s ease;
}

.auth-btn:hover::after {
    transform: translateX(0);
}

.auth-btn:active {
    transform: scale(0.98);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .auth-form-container {
        padding: 0 0.5rem;
    }
    
    .auth-btn {
        font-size: 0.95rem;
    }
    
    .flex.items-start.gap-2\.5 {
        flex-direction: column;
    }
}
</style>
@endsection