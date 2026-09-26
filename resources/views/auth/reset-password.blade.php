@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<div class="auth-form-container">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-key text-[#1a2a4a] text-xl"></i>
            Reset Password
        </h2>
        <p class="text-sm text-gray-500 mt-1.5">Enter your new password below.</p>
    </div>

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

    <!-- Session Status -->
    @if(session('status'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2.5">
            <i class="fas fa-check-circle text-green-500 text-sm"></i>
            {{ session('status') }}
        </div>
    @endif

    <!-- Reset Password Form -->
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">
                New Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                </div>
                <input id="password" 
                       type="password" 
                       name="password" 
                       required
                       class="auth-input w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition @error('password') border-red-500 @enderror"
                       placeholder="Enter new password">
                <button type="button" 
                        onclick="togglePasswordVisibility('password', 'passwordToggleIcon')" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-eye" id="passwordToggleIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                    {{ $message }}
                </p>
            @enderror
            <p class="mt-1.5 text-xs text-gray-400 flex items-center gap-1.5">
                <i class="fas fa-info-circle text-[10px]"></i>
                Password must be at least 8 characters
            </p>
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">
                Confirm Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="fas fa-check-circle text-gray-400 text-sm"></i>
                </div>
                <input id="password_confirmation" 
                       type="password" 
                       name="password_confirmation" 
                       required
                       class="auth-input w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1a2a4a] focus:border-transparent transition"
                       placeholder="Confirm new password">
                <button type="button" 
                        onclick="togglePasswordVisibility('password_confirmation', 'confirmToggleIcon')" 
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition">
                    <i class="fas fa-eye" id="confirmToggleIcon"></i>
                </button>
            </div>
        </div>

        <!-- Password Strength Indicator (Optional) -->
        <div class="password-strength hidden">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Password strength:</span>
                <div class="flex gap-1">
                    <div class="w-8 h-1 bg-gray-200 rounded-full"></div>
                    <div class="w-8 h-1 bg-gray-200 rounded-full"></div>
                    <div class="w-8 h-1 bg-gray-200 rounded-full"></div>
                    <div class="w-8 h-1 bg-gray-200 rounded-full"></div>
                </div>
                <span class="text-xs font-medium text-gray-400" id="strengthText">Weak</span>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-btn w-full py-3.5 px-4 bg-[#1a2a4a] hover:bg-[#0f1a30] text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition flex items-center justify-center gap-2 group">
            <i class="fas fa-key group-hover:scale-110 transition-transform"></i>
            Reset Password
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

<!-- JavaScript for Password Toggle -->
<script>
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Simple password strength indicator (optional)
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthContainer = document.querySelector('.password-strength');
    const strengthText = document.getElementById('strengthText');
    const bars = document.querySelectorAll('.password-strength .w-8');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            const strength = getPasswordStrength(value);
            
            // Update bars
            bars.forEach((bar, index) => {
                if (index < strength.score) {
                    bar.classList.remove('bg-gray-200');
                    bar.classList.add(strength.color);
                } else {
                    bar.classList.remove('bg-red-500', 'bg-yellow-500', 'bg-green-500', 'bg-emerald-500');
                    bar.classList.add('bg-gray-200');
                }
            });
            
            // Update text
            strengthText.textContent = strength.label;
            strengthText.className = 'text-xs font-medium ' + strength.textColor;
            
            // Show/hide container
            if (value.length > 0) {
                strengthContainer.classList.remove('hidden');
            } else {
                strengthContainer.classList.add('hidden');
            }
        });
    }
});

function getPasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    // Normalize score to 0-4
    score = Math.min(Math.floor(score / 2), 4);
    
    const strengths = [
        { score: 0, label: 'Weak', color: 'bg-red-500', textColor: 'text-red-500' },
        { score: 1, label: 'Weak', color: 'bg-red-500', textColor: 'text-red-500' },
        { score: 2, label: 'Fair', color: 'bg-yellow-500', textColor: 'text-yellow-600' },
        { score: 3, label: 'Good', color: 'bg-green-500', textColor: 'text-green-600' },
        { score: 4, label: 'Strong', color: 'bg-emerald-500', textColor: 'text-emerald-600' }
    ];
    
    return strengths[score] || strengths[0];
}
</script>

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

/* Password strength bars transition */
.password-strength .w-8 {
    transition: all 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .auth-form-container {
        padding: 0 0.5rem;
    }
    
    .auth-btn {
        font-size: 0.95rem;
    }
}
</style>
@endsection